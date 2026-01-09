<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
            ->aktif()
            ->orderBy('tanggal_kembali_rencana')
            ->paginate(10);
        
        return view('user.sewa.aktif', compact('sewas'));
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
        $denda = $sewa->hitungDenda();
        
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
        
        $sewa = auth()->user()->sewas()
            ->aktif()
            ->findOrFail($id);
        
        // Validate return date
        $tanggalKembali = Carbon::parse($request->tanggal_kembali);
        $tanggalMulai = Carbon::parse($sewa->tanggal_mulai);
        
        if ($tanggalKembali->lt($tanggalMulai)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal kembali tidak boleh sebelum tanggal mulai sewa.'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            // Calculate late return days
            $tanggalKembaliRencana = Carbon::parse($sewa->tanggal_kembali_rencana);
            $keterlambatan = 0;
            
            if ($tanggalKembali->gt($tanggalKembaliRencana)) {
                $keterlambatan = $tanggalKembali->diffInDays($tanggalKembaliRencana);
            }
            
            // Calculate fines
            $tarifDenda = Konfigurasi::getValue('denda_per_hari', 10000);
            $dendaKeterlambatan = $keterlambatan * $tarifDenda;
            
            // Calculate damage fines
            $dendaKerusakan = 0;
            if ($request->kondisi_alat !== 'baik') {
                $hargaProduk = $sewa->produk->harga_beli;
                
                switch ($request->kondisi_alat) {
                    case 'rusak_ringan':
                        $dendaKerusakan = $hargaProduk * 0.1; // 10%
                        break;
                    case 'rusak_berat':
                        $dendaKerusakan = $hargaProduk * 0.5; // 50%
                        break;
                    case 'hilang':
                        $dendaKerusakan = $hargaProduk; // 100%
                        break;
                }
            }
            
            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;
            
            // Create pengembalian record
            $pengembalian = Pengembalian::create([
                'sewa_id' => $sewa->id,
                'tanggal_kembali' => $tanggalKembali,
                'keterlambatan_hari' => $keterlambatan,
                'kondisi_alat' => $request->kondisi_alat,
                'catatan_kondisi' => $request->catatan_kondisi,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'denda_kerusakan' => $dendaKerusakan,
                'total_denda' => $totalDenda,
                'status' => 'menunggu'
            ]);
            
            // Update rental status
            $sewa->status = 'selesai';
            $sewa->tanggal_kembali_aktual = $tanggalKembali;
            $sewa->denda = $totalDenda;
            $sewa->save();
            
            // Return stock
            $sewa->produk->updateStokSewa($sewa->jumlah_hari, 'masuk');
            
            DB::commit();
            
            // Create notification for admin
            \App\Models\Notifikasi::createNotifikasi(
                null, // Admin notification
                'Pengembalian Alat Baru',
                'Pengembalian untuk sewa ' . $sewa->kode_sewa . ' menunggu verifikasi.',
                'sewa'
            );
            
            // Create notification for user
            \App\Models\Notifikasi::createNotifikasi(
                $sewa->user_id,
                'Pengembalian Berhasil',
                'Pengembalian alat untuk sewa ' . $sewa->kode_sewa . ' telah diajukan. Menunggu verifikasi admin.',
                'success',
                route('user.sewa.show', $sewa->id)
            );
            
            // If there's a fine, create denda record
            if ($totalDenda > 0) {
                \App\Models\Denda::create([
                    'pengembalian_id' => $pengembalian->id,
                    'user_id' => $sewa->user_id,
                    'kode_denda' => \App\Models\Denda::generateKodeDenda(),
                    'tarif_denda_per_hari' => $tarifDenda,
                    'jumlah_hari_terlambat' => $keterlambatan,
                    'jumlah_denda' => $totalDenda,
                    'status_pembayaran' => 'belum_dibayar',
                    'tanggal_jatuh_tempo' => Carbon::now()->addDays(7),
                    'keterangan' => 'Denda keterlambatan dan kerusakan alat'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Pengembalian berhasil diajukan. Menunggu verifikasi admin.',
                'redirect' => route('user.sewa.show', $sewa->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function calculateDenda(Request $request)
    {
        $request->validate([
            'sewa_id' => 'required|exists:sewas,id',
            'tanggal_kembali' => 'required|date',
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang'
        ]);
        
        $sewa = Sewa::findOrFail($request->sewa_id);
        
        // Validate user owns this rental
        if ($sewa->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }
        
        $tanggalKembali = Carbon::parse($request->tanggal_kembali);
        $tanggalKembaliRencana = Carbon::parse($sewa->tanggal_kembali_rencana);
        
        // Calculate late return days
        $keterlambatan = 0;
        if ($tanggalKembali->gt($tanggalKembaliRencana)) {
            $keterlambatan = $tanggalKembali->diffInDays($tanggalKembaliRencana);
        }
        
        // Calculate fines
        $tarifDenda = Konfigurasi::getValue('denda_per_hari', 10000);
        $dendaKeterlambatan = $keterlambatan * $tarifDenda;
        
        // Calculate damage fines
        $dendaKerusakan = 0;
        if ($request->kondisi_alat !== 'baik') {
            $hargaProduk = $sewa->produk->harga_beli;
            
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
                'denda_keterlambatan' => $dendaKeterlambatan,
                'denda_kerusakan' => $dendaKerusakan,
                'total_denda' => $totalDenda,
                'tarif_denda_per_hari' => $tarifDenda,
                'harga_produk' => $sewa->produk->harga_beli
            ]
        ]);
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
            $sewa->tanggal_selesai = Carbon::parse($sewa->tanggal_selesai)->addDays($request->tambahan_hari);
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
                route('user.transaksi.show', $transaksi->id)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Sewa berhasil diperpanjang. Silakan lakukan pembayaran tambahan.',
                'redirect' => route('user.transaksi.show', $transaksi->id)
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