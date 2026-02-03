<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use App\Models\Sewa;
use App\Models\Pengembalian;
use App\Models\Produk;
use App\Models\Konfigurasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SewaController extends Controller
{
    public function index()
    {
        // Get products available for rent
        $produks = Produk::with('kategori')
            ->tipeSewa()
            ->active()
            ->stokTersedia()
            ->paginate(12);

        // Get featured rental products
        $featuredProducts = Produk::with('kategori')
            ->tipeSewa()
            ->active()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $kategoris = \App\Models\Kategori::active()->get();

        return view('user.sewa.index', compact('produks', 'featuredProducts', 'kategoris'));
    }

    public function aktif()
    {
        $sewas = auth()->user()->sewas()
            ->with('produk')
            ->orderBy('tanggal_kembali_rencana')
            ->paginate(10);

        return view('user.sewa.aktif', compact('sewas'));
    }

    public function struk(Request $request)
    {
        $data = Sewa::find($request->struk);
        return view('user.sewa.struk', compact('data'));
    }

    public function riwayat()
    {
        $sewas = auth()->user()->sewas()
            ->with('produk')
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->paginate(10);

        return view('user.sewa.riwayat', compact('sewas'));
    }

    public function show($id)
    {
        $sewa = auth()->user()->sewas()
            ->with(['produk', 'pengembalian'])
            ->findOrFail($id);

        // Calculate remaining days and potential fines
        $sisaHari = $sewa->sisa_hari;
        $keterlambatan = $sewa->hitungKeterlambatan();
        $denda = $sewa->denda;

        // Get pengembalian if exists
        $pengembalian = $sewa->pengembalian;

        return view('user.sewa.show', compact('sewa', 'sisaHari', 'keterlambatan', 'denda', 'pengembalian'));
    }

    public function pengembalian(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali' => 'required|date',
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan_kondisi' => 'nullable|string|max:500'
        ]);

        $userSewas = auth()->user()->sewas()
            ->with('produk')
            ->get()
            ->map(function ($sewa) {
                return [
                    'id' => $sewa->id,
                    'kode' => $sewa->kode_sewa,
                    'status' => $sewa->status,
                    'produk_id' => $sewa->produk_id
                ];
            });

        
        $sewa = Sewa::with('produk')
            ->where('status', 'aktif')
            ->find($id);

        $tanggalKembali = Carbon::parse($request->tanggal_kembali);
        $tanggalMulai = Carbon::parse($sewa->tanggal_mulai);
        $tanggalKembaliRencana = Carbon::parse($sewa->tanggal_kembali_rencana);

        // Validasi tanggal kembali
        if ($tanggalKembali->lt($tanggalMulai)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal kembali tidak boleh sebelum tanggal mulai sewa.'
            ], 400);
        }

        DB::beginTransaction();

        // Perbaikan perhitungan keterlambatan
        if ($tanggalKembali->gt($tanggalKembaliRencana)) {
            $keterlambatan = $tanggalKembaliRencana->diffInDays($tanggalKembali);
        } else {
            $keterlambatan = 0;
        }
        
        $mulaiDendaHariKe = 3;

        // Hitung hari yang benar-benar kena denda
        $hariKenaDenda = $keterlambatan >= $mulaiDendaHariKe
            ? ($keterlambatan - ($mulaiDendaHariKe - 1)) // telat 3 => 1, telat 4 => 2
            : 0;

        $tarifDenda = ($sewa->total_harga * 0.20);
        $dendaKeterlambatan = $hariKenaDenda * $tarifDenda;

        // Hitung denda kerusakan
        $dendaKerusakan = 0;

        if ($request->kondisi_alat !== 'baik') {
            $hargaProduk = $sewa->produk->harga_beli;

            switch ($request->kondisi_alat) {
                case 'rusak_ringan':
                    break;
                case 'rusak_berat':
                    $dendaKerusakan = $hargaProduk * 0.5;
                    break;
                case 'hilang':
                    $dendaKerusakan = $hargaProduk;
                    break;
            }
        }

        $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

        // Buat record pengembalian
        $pengembalianData = [
            'sewa_id' => $sewa->id,
            'tanggal_kembali' => $tanggalKembali,
            'keterlambatan_hari' => $keterlambatan,
            'kondisi_alat' => $request->kondisi_alat,
            'catatan_kondisi' => $request->catatan_kondisi,
            'denda_keterlambatan' => $dendaKeterlambatan,
            'denda_kerusakan' => $dendaKerusakan,
            'total_denda' => $totalDenda,
            // 'status' => 'menunggu',
            'created_at' => now(),
            'updated_at' => now()
        ];


        $pengembalian = Pengembalian::create($pengembalianData);

        // Update status sewa
        $sewa->update([
            'status' => 'selesai',
            'tanggal_kembali_aktual' => $tanggalKembali,
            'denda' => $totalDenda
        ]);

        // Kembalikan stok
        try {
            if (method_exists($sewa->produk, 'updateStokSewa')) {
                $sewa->produk->updateStokSewa($sewa->jumlah_hari, 'masuk');
            } else {
                $sewa->produk->increment('stok_tersedia', $sewa->jumlah_hari);
            }
        } catch (\Exception $e) {
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian berhasil diajukan. Menunggu verifikasi admin.',
            'data' => [
                'pengembalian_id' => $pengembalian->id,
                'keterlambatan' => $keterlambatan,
                'total_denda' => $totalDenda,
                'formatted_denda' => 'Rp ' . number_format($totalDenda, 0, ',', '.')
            ],
            'redirect' => route('user.sewa.index', $sewa->id)
        ]);
    }

    public function calculateDenda(Request $request)
    {
        try {
            $request->validate([
                'sewa_id' => 'required|exists:sewas,id',
                'tanggal_kembali' => 'required|date',
                'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang'
            ]);

            $sewa = Sewa::with('produk')->findOrFail($request->sewa_id);

            $tanggalKembali = Carbon::parse($request->tanggal_kembali);
            $tanggalMulai = Carbon::parse($sewa->tanggal_mulai);
            $tanggalKembaliRencana = Carbon::parse($sewa->tanggal_kembali_rencana);

            // Validasi tanggal kembali
            if ($tanggalKembali->lt($tanggalMulai)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal kembali tidak boleh sebelum tanggal mulai sewa.'
                ], 400);
            }

            // Hitung keterlambatan (hari)
            if ($tanggalKembali->gt($tanggalKembaliRencana)) {
                $keterlambatan = $tanggalKembaliRencana->diffInDays($tanggalKembali);
            } else {
                $keterlambatan = 0;
            }

            // =========================
            // DENDA KETERLAMBATAN (SESUAI RULE BARU)
            // - Hari 1-2 telat: TIDAK kena denda
            // - Mulai hari ke-3: kena denda 20% / hari
            // =========================
            $mulaiDendaHariKe = 3;
            $hariKenaDenda = $keterlambatan >= $mulaiDendaHariKe
                ? ($keterlambatan - ($mulaiDendaHariKe - 1)) // telat 3 => 1, telat 4 => 2, dst
                : 0;

            $tarifDenda = ($sewa->total_harga * 0.20);
            $dendaKeterlambatan = $hariKenaDenda * $tarifDenda;

            // =========================
            // HANDLE harga_beli null/0
            // =========================
            $hargaProduk = $sewa->produk->harga_beli;

            if (is_null($hargaProduk) || $hargaProduk == 0) {
                if (isset($sewa->produk->harga_sewa) && $sewa->produk->harga_sewa > 0) {
                    $hargaProduk = $sewa->produk->harga_sewa;
                } elseif (isset($sewa->produk->harga) && $sewa->produk->harga > 0) {
                    $hargaProduk = $sewa->produk->harga;
                } else {
                    $hargaProduk = 1000000; // default
                }
            }

            // =========================
            // DENDA KERUSAKAN
            // =========================
            $dendaKerusakan = 0;

            if ($request->kondisi_alat !== 'baik') {
                switch ($request->kondisi_alat) {
                    case 'rusak_ringan':
                        $dendaKerusakan = $hargaProduk * 0.1;
                        break;
                    case 'rusak_berat':
                        $dendaKerusakan = $hargaProduk * 0.5;
                        break;
                    case 'hilang':
                        $dendaKerusakan = $hargaProduk;
                        break;
                }
            }

            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

            return response()->json([
                'success' => true,
                'data' => [
                    'keterlambatan_hari' => $keterlambatan,

                    // tambahan agar UI bisa jelasin denda mulai hari ke-3
                    'mulai_denda_hari_ke' => $mulaiDendaHariKe,
                    'hari_kena_denda' => $hariKenaDenda,

                    'tarif_denda_per_hari' => $tarifDenda,
                    'denda_keterlambatan' => $dendaKeterlambatan,
                    'denda_kerusakan' => $dendaKerusakan,
                    'total_denda' => $totalDenda,
                    'harga_produk' => $hargaProduk,
                    'harga_produk_original' => $sewa->produk->harga_beli,
                    'formatted' => [
                        'denda_keterlambatan' => 'Rp ' . number_format($dendaKeterlambatan, 0, ',', '.'),
                        'denda_kerusakan' => 'Rp ' . number_format($dendaKerusakan, 0, ',', '.'),
                        'total_denda' => 'Rp ' . number_format($totalDenda, 0, ',', '.'),
                        'tarif_denda_per_hari' => 'Rp ' . number_format($tarifDenda, 0, ',', '.'),
                        'harga_produk' => 'Rp ' . number_format($hargaProduk, 0, ',', '.')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::debug('=== DEBUG CALCULATE DENDA END - ERROR ===');
            \Log::debug('Error Details:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }


    public function extend(Request $request, $id)
    {
        $request->validate([
            'tambahan_hari' => 'required|integer|min:1|max:30',
            'alasan' => 'nullable|string|max:500'
        ]);

        $sewa = auth()->user()->sewas()
            ->aktif()
            ->findOrFail($id);

        // Check max rental days
        $totalHari = $sewa->jumlah_hari + $request->tambahan_hari;
        $maxHari = Konfigurasi::getValue('max_hari_sewa', 30);

        if ($totalHari > $maxHari) {
            return response()->json([
                'success' => false,
                'message' => "Maksimal sewa adalah $maxHari hari."
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Calculate additional cost
            $hargaPerHari = $sewa->produk->getHargaSewa($sewa->durasi);
            $tambahanBiaya = $hargaPerHari * $request->tambahan_hari;

            // Extend rental
            $sewa->jumlah_hari = $totalHari;
            $sewa->tanggal_selesai = Carbon::parse($sewa->tanggal_selesai)->addDays((int) $request->tambahan_hari);
            $sewa->tanggal_kembali_rencana = $sewa->tanggal_selesai;
            $sewa->total_harga += $tambahanBiaya;
            $sewa->save();

            // Create new transaction for extension
            $transaksi = \App\Models\Transaksi::create([
                'kode_transaksi' => \App\Models\Transaksi::generateKodeTransaksi(),
                'user_id' => auth()->id(),
                'tipe' => 'penyewaan',
                'total_harga' => $tambahanBiaya,
                'total_bayar' => $tambahanBiaya * 1.11, // Include tax
                'status' => 'pending',
                'metode_pembayaran' => 'transfer_bank',
                'catatan' => "Perpanjangan sewa {$sewa->kode_sewa} - {$request->alasan}"
            ]);

            DB::commit();

            // Create notification
            \App\Models\Notifikasi::createNotifikasi(
                auth()->id(),
                'Sewa Diperpanjang',
                "Sewa {$sewa->kode_sewa} diperpanjang {$request->tambahan_hari} hari. Silakan lakukan pembayaran.",
                'sewa',
                route('user.transaksi.index', $transaksi->id)
            );

            return response()->json([
                'success' => true,
                'message' => 'Sewa berhasil diperpanjang. Silakan lakukan pembayaran tambahan.',
                'redirect' => route('user.transaksi.index', $transaksi->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
