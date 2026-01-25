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
        $denda = $sewa->hitungDenda();
        
        // Get pengembalian if exists
        $pengembalian = $sewa->pengembalian;
        
        return view('user.sewa.show', compact('sewa', 'sisaHari', 'keterlambatan', 'denda', 'pengembalian'));
    }
    
public function pengembalian(Request $request, $id)
{
    \Log::debug('=== DEBUG PENGEMBALIAN START ===');
    \Log::debug('User ID:', ['user_id' => auth()->id()]);
    \Log::debug('Sewa ID:', ['sewa_id' => $id]);
    \Log::debug('Request Data:', $request->all());
    \Log::debug('Auth User Sewas Count:', [
        'count' => auth()->user()->sewas()->count(),
        'active_count' => auth()->user()->sewas()->where('status', 'aktif')->count()
    ]);
    
    $request->validate([
        'tanggal_kembali' => 'required|date',
        'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
        'catatan_kondisi' => 'nullable|string|max:500'
    ]);
    
    try {
        // DEBUG: Cek user sewa
        $userSewas = auth()->user()->sewas()
            ->with('produk')
            ->get()
            ->map(function($sewa) {
                return [
                    'id' => $sewa->id,
                    'kode' => $sewa->kode_sewa,
                    'status' => $sewa->status,
                    'produk_id' => $sewa->produk_id
                ];
            });
        \Log::debug('All User Sewas:', $userSewas->toArray());
        
        $sewa = auth()->user()->sewas()
            ->with('produk')
            ->where('status', 'aktif')
            ->find($id);
            
        \Log::debug('Sewa Query Result:', [
            'found' => !is_null($sewa),
            'sewa_data' => $sewa ? [
                'id' => $sewa->id,
                'kode' => $sewa->kode_sewa,
                'status' => $sewa->status,
                'produk_id' => $sewa->produk_id,
                'produk_name' => $sewa->produk->nama ?? 'null',
                'user_id' => $sewa->user_id,
                'auth_id' => auth()->id()
            ] : null
        ]);
        
        if (!$sewa) {
            \Log::debug('Sewa tidak ditemukan atau tidak aktif:', [
                'search_id' => $id,
                'user_id' => auth()->id(),
                'is_owner' => $sewa ? ($sewa->user_id === auth()->id()) : false
            ]);
        }
        
        $sewa = auth()->user()->sewas()
            ->with('produk')
            ->where('status', 'aktif')
            ->findOrFail($id);
        
        \Log::debug('Sewa ditemukan:', [
            'id' => $sewa->id,
            'kode' => $sewa->kode_sewa,
            'tanggal_mulai' => $sewa->tanggal_mulai,
            'tanggal_kembali_rencana' => $sewa->tanggal_kembali_rencana,
            'jumlah_hari' => $sewa->jumlah_hari,
            'produk' => [
                'id' => $sewa->produk->id,
                'nama' => $sewa->produk->nama,
                'harga_beli' => $sewa->produk->harga_beli,
                'stok_tersedia' => $sewa->produk->stok_tersedia
            ]
        ]);
        
        $tanggalKembali = Carbon::parse($request->tanggal_kembali);
        $tanggalMulai = Carbon::parse($sewa->tanggal_mulai);
        $tanggalKembaliRencana = Carbon::parse($sewa->tanggal_kembali_rencana);
        
        \Log::debug('Tanggal Perhitungan:', [
            'tanggal_kembali_input' => $request->tanggal_kembali,
            'tanggal_kembali_parsed' => $tanggalKembali->toDateString(),
            'tanggal_mulai' => $tanggalMulai->toDateString(),
            'tanggal_kembali_rencana' => $tanggalKembaliRencana->toDateString()
        ]);
        
        // Validasi tanggal kembali
        if ($tanggalKembali->lt($tanggalMulai)) {
            \Log::debug('Validasi gagal: Tanggal kembali sebelum tanggal mulai', [
                'tanggal_kembali' => $tanggalKembali->toDateString(),
                'tanggal_mulai' => $tanggalMulai->toDateString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Tanggal kembali tidak boleh sebelum tanggal mulai sewa.'
            ], 400);
        }
        
        DB::beginTransaction();
        \Log::debug('Transaction dimulai');
        
        // DEBUG: Hitung keterlambatan dengan detail
        \Log::debug('Perhitungan Keterlambatan:', [
            'tanggal_kembali' => $tanggalKembali->toDateString(),
            'tanggal_kembali_rencana' => $tanggalKembaliRencana->toDateString(),
            'is_late' => $tanggalKembali->gt($tanggalKembaliRencana),
            'diff_in_days_raw' => $tanggalKembali->diffInDays($tanggalKembaliRencana),
            'diff_in_days_rencana_to_kembali' => $tanggalKembaliRencana->diffInDays($tanggalKembali)
        ]);
        
        // Perbaikan perhitungan keterlambatan
        $keterlambatan = max(0, $tanggalKembali->diffInDays($tanggalKembaliRencana, false));
        \Log::debug('Keterlambatan Final:', ['hari' => $keterlambatan]);
        
        // Hitung denda keterlambatan
        $tarifDenda = Konfigurasi::getValue('denda_per_hari', 10000);
        \Log::debug('Konfigurasi Denda:', [
            'tarif_denda' => $tarifDenda,
            'source' => 'Konfigurasi::getValue'
        ]);
        
        $dendaKeterlambatan = $keterlambatan * $tarifDenda;
        
        // Hitung denda kerusakan
        $dendaKerusakan = 0;
        \Log::debug('Kondisi Alat:', [
            'kondisi' => $request->kondisi_alat,
            'is_baik' => $request->kondisi_alat === 'baik'
        ]);
        
        if ($request->kondisi_alat !== 'baik') {
            $hargaProduk = $sewa->produk->harga_beli;
            \Log::debug('Harga Produk untuk Denda:', [
                'harga_beli' => $hargaProduk,
                'formatted' => 'Rp ' . number_format($hargaProduk, 0, ',', '.')
            ]);
            
            switch ($request->kondisi_alat) {
                case 'rusak_ringan':
                    $dendaKerusakan = $hargaProduk * 0.1;
                    \Log::debug('Denda Rusak Ringan:', [
                        'persentase' => '10%',
                        'hitung' => "$hargaProduk * 0.1 = $dendaKerusakan"
                    ]);
                    break;
                case 'rusak_berat':
                    $dendaKerusakan = $hargaProduk * 0.5;
                    \Log::debug('Denda Rusak Berat:', [
                        'persentase' => '50%',
                        'hitung' => "$hargaProduk * 0.5 = $dendaKerusakan"
                    ]);
                    break;
                case 'hilang':
                    $dendaKerusakan = $hargaProduk;
                    \Log::debug('Denda Hilang:', [
                        'persentase' => '100%',
                        'hitung' => "$hargaProduk * 1 = $dendaKerusakan"
                    ]);
                    break;
            }
        }
        
        $totalDenda = $dendaKeterlambatan + $dendaKerusakan;
        
        \Log::debug('Ringkasan Perhitungan Denda:', [
            'keterlambatan_hari' => $keterlambatan,
            'tarif_denda_per_hari' => $tarifDenda,
            'denda_keterlambatan' => $dendaKeterlambatan,
            'denda_kerusakan' => $dendaKerusakan,
            'total_denda' => $totalDenda,
            'formatted' => [
                'denda_keterlambatan' => 'Rp ' . number_format($dendaKeterlambatan, 0, ',', '.'),
                'denda_kerusakan' => 'Rp ' . number_format($dendaKerusakan, 0, ',', '.'),
                'total_denda' => 'Rp ' . number_format($totalDenda, 0, ',', '.')
            ]
        ]);
        
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
            'status' => 'menunggu',
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        \Log::debug('Data Pengembalian untuk Insert:', $pengembalianData);
        
        $pengembalian = Pengembalian::create($pengembalianData);
        \Log::debug('Pengembalian Created:', [
            'id' => $pengembalian->id,
            'created_at' => $pengembalian->created_at
        ]);
        
        // Update status sewa
        $sewa->update([
            'status' => 'selesai',
            'tanggal_kembali_aktual' => $tanggalKembali,
            'denda' => $totalDenda
        ]);
        
        \Log::debug('Sewa Updated:', [
            'status_before' => 'aktif',
            'status_after' => 'selesai',
            'denda_set' => $totalDenda
        ]);
        
        // DEBUG: Cek metode stok
        \Log::debug('Cek Metode Stok:', [
            'produk_id' => $sewa->produk->id,
            'has_updateStokSewa' => method_exists($sewa->produk, 'updateStokSewa'),
            'stok_sebelum' => $sewa->produk->stok_tersedia,
            'jumlah_hari' => $sewa->jumlah_hari
        ]);
        
        // Kembalikan stok
        try {
            if (method_exists($sewa->produk, 'updateStokSewa')) {
                $sewa->produk->updateStokSewa($sewa->jumlah_hari, 'masuk');
                \Log::debug('Stok setelah updateStokSewa:', [
                    'stok_setelah' => $sewa->produk->fresh()->stok_tersedia
                ]);
            } else {
                $sewa->produk->increment('stok_tersedia', $sewa->jumlah_hari);
                \Log::debug('Stok setelah increment:', [
                    'stok_setelah' => $sewa->produk->fresh()->stok_tersedia
                ]);
            }
        } catch (\Exception $e) {
            \Log::debug('Error update stok:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        DB::commit();
        \Log::debug('Transaction committed');
        
        \Log::debug('=== DEBUG PENGEMBALIAN END - SUKSES ===');
        
        return response()->json([
            'success' => true,
            'message' => 'Pengembalian berhasil diajukan. Menunggu verifikasi admin.',
            'data' => [
                'pengembalian_id' => $pengembalian->id,
                'keterlambatan' => $keterlambatan,
                'total_denda' => $totalDenda,
                'formatted_denda' => 'Rp ' . number_format($totalDenda, 0, ',', '.')
            ],
            'redirect' => route('user.sewa.show', $sewa->id)
        ]);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        DB::rollBack();
        \Log::debug('=== DEBUG PENGEMBALIAN END - MODEL NOT FOUND ===');
        \Log::debug('Error Detail:', [
            'message' => $e->getMessage(),
            'model' => $e->getModel(),
            'ids' => $e->getIds()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Data sewa tidak ditemukan atau sudah tidak aktif.',
            'debug' => [
                'user_id' => auth()->id(),
                'sewa_id' => $id,
                'model' => $e->getModel()
            ]
        ], 404);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::debug('=== DEBUG PENGEMBALIAN END - EXCEPTION ===');
        \Log::debug('Error Full:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi admin.',
            'error_detail' => config('app.debug') ? [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ] : null
        ], 500);
    }
}
    
public function calculateDenda(Request $request)
{
    \Log::debug('=== DEBUG CALCULATE DENDA START ===');
    \Log::debug('Request Data:', $request->all());
    \Log::debug('Auth User ID:', ['user_id' => auth()->id()]);
    
    try {
        $request->validate([
            'sewa_id' => 'required|exists:sewas,id',
            'tanggal_kembali' => 'required|date',
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang'
        ]);
        
        \Log::debug('Validation passed');
        
        $sewa = Sewa::with('produk')->find($request->sewa_id);
        
        \Log::debug('Sewa Found:', [
            'found' => !is_null($sewa),
            'sewa_data' => $sewa ? [
                'id' => $sewa->id,
                'kode' => $sewa->kode_sewa,
                'user_id' => $sewa->user_id,
                'auth_id' => auth()->id(),
                'status' => $sewa->status,
                'produk' => $sewa->produk ? [
                    'id' => $sewa->produk->id,
                    'nama' => $sewa->produk->nama,
                    'harga_beli' => $sewa->produk->harga_beli,
                    'harga_sewa' => $sewa->produk->harga_sewa ?? 'null',
                    'harga' => $sewa->produk->harga ?? 'null'
                ] : null
            ] : null
        ]);
        
        // VALIDASI HARGA PRODUK
        if ($sewa && $sewa->produk && is_null($sewa->produk->harga_beli)) {
            \Log::warning('Harga beli produk NULL, cek field alternatif:', [
                'produk_id' => $sewa->produk->id,
                'fields' => array_keys($sewa->produk->getAttributes())
            ]);
            
            // Coba ambil harga dari field alternatif
            $hargaAlternatif = $sewa->produk->harga_sewa ?? $sewa->produk->harga ?? 0;
            \Log::debug('Harga alternatif ditemukan:', ['harga' => $hargaAlternatif]);
        }
        
        if (!$sewa) {
            \Log::debug('Sewa tidak ditemukan di database');
            return response()->json([
                'success' => false,
                'message' => 'Data sewa tidak ditemukan.'
            ], 404);
        }
        
        $sewa = Sewa::with('produk')->findOrFail($request->sewa_id);
        
        // Validasi pemilik sewa
        if ($sewa->user_id !== auth()->id()) {
            \Log::debug('Akses ditolak: Bukan pemilik sewa');
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke data sewa ini.'
            ], 403);
        }
        
        // Validasi status sewa
        if ($sewa->status !== 'aktif') {
            \Log::debug('Sewa tidak aktif:', ['status' => $sewa->status]);
            return response()->json([
                'success' => false,
                'message' => 'Sewa sudah tidak aktif.'
            ], 400);
        }
        
        \Log::debug('Sewa Detail:', [
            'tanggal_mulai' => $sewa->tanggal_mulai,
            'tanggal_kembali_rencana' => $sewa->tanggal_kembali_rencana,
            'produk_harga_beli' => $sewa->produk->harga_beli,
            'produk_harga_sewa' => $sewa->produk->harga_sewa ?? 'null',
            'produk_harga' => $sewa->produk->harga ?? 'null'
        ]);
        
        $tanggalKembali = Carbon::parse($request->tanggal_kembali);
        $tanggalMulai = Carbon::parse($sewa->tanggal_mulai);
        $tanggalKembaliRencana = Carbon::parse($sewa->tanggal_kembali_rencana);
        
        \Log::debug('Tanggal Parsing:', [
            'input_tanggal_kembali' => $request->tanggal_kembali,
            'parsed_tanggal_kembali' => $tanggalKembali->toDateString(),
            'tanggal_mulai' => $tanggalMulai->toDateString(),
            'tanggal_kembali_rencana' => $tanggalKembaliRencana->toDateString(),
            'timezone' => config('app.timezone')
        ]);
        
        // Validasi tanggal kembali
        if ($tanggalKembali->lt($tanggalMulai)) {
            \Log::debug('Tanggal kembali sebelum tanggal mulai', [
                'tanggal_kembali' => $tanggalKembali->toDateString(),
                'tanggal_mulai' => $tanggalMulai->toDateString(),
                'is_before' => $tanggalKembali->lt($tanggalMulai)
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Tanggal kembali tidak boleh sebelum tanggal mulai sewa.'
            ], 400);
        }
        
        // PERBAIKAN: Hitung keterlambatan dengan benar
        \Log::debug('Keterlambatan Check RAW:', [
            'tanggal_kembali' => $tanggalKembali->toDateTimeString(),
            'tanggal_kembali_rencana' => $tanggalKembaliRencana->toDateTimeString(),
            'is_after' => $tanggalKembali->gt($tanggalKembaliRencana),
            'diff_in_days_1' => $tanggalKembali->diffInDays($tanggalKembaliRencana),
            'diff_in_days_2' => $tanggalKembali->diffInDays($tanggalKembaliRencana, false),
            'float_diff' => $tanggalKembali->floatDiffInDays($tanggalKembaliRencana),
            'float_diff_abs' => abs($tanggalKembali->floatDiffInDays($tanggalKembaliRencana))
        ]);
        
        // PERBAIKAN 1: Gunakan metode yang benar untuk keterlambatan
        $keterlambatan = 0;
        if ($tanggalKembali->gt($tanggalKembaliRencana)) {
            // Gunakan diff dengan parameter false untuk mendapatkan nilai signed
            $diff = $tanggalKembali->diffInDays($tanggalKembaliRencana, false);
            $keterlambatan = max(0, $diff);
            
            \Log::debug('Perhitungan Keterlambatan Diperbaiki:', [
                'diff_signed' => $diff,
                'keterlambatan_final' => $keterlambatan,
                'explanation' => 'max(0, ' . $diff . ') = ' . $keterlambatan
            ]);
        }
        
        \Log::debug('Keterlambatan Final:', ['hari' => $keterlambatan]);
        
        // Hitung denda keterlambatan
        $tarifDenda = Konfigurasi::getValue('denda_per_hari', 10000);
        \Log::debug('Tarif Denda:', [
            'tarif' => $tarifDenda,
            'source' => 'Konfigurasi::getValue'
        ]);
        
        $dendaKeterlambatan = $keterlambatan * $tarifDenda;
        \Log::debug('Denda Keterlambatan:', [
            'hitung' => "$keterlambatan * $tarifDenda = $dendaKeterlambatan"
        ]);
        
        // PERBAIKAN 2: Handle harga_beli yang null
        $hargaProduk = $sewa->produk->harga_beli;
        
        // Jika harga_beli null, gunakan harga alternatif
        if (is_null($hargaProduk) || $hargaProduk == 0) {
            \Log::warning('harga_beli NULL atau 0, mencari alternatif');
            
            // Coba field alternatif berdasarkan struktur database umum
            if (isset($sewa->produk->harga_sewa) && $sewa->produk->harga_sewa > 0) {
                $hargaProduk = $sewa->produk->harga_sewa;
                \Log::debug('Menggunakan harga_sewa sebagai alternatif:', ['harga' => $hargaProduk]);
            } elseif (isset($sewa->produk->harga) && $sewa->produk->harga > 0) {
                $hargaProduk = $sewa->produk->harga;
                \Log::debug('Menggunakan harga sebagai alternatif:', ['harga' => $hargaProduk]);
            } else {
                // Default harga jika tidak ada
                $hargaProduk = 1000000; // Rp 1.000.000 default
                \Log::warning('Menggunakan harga default:', ['harga_default' => $hargaProduk]);
            }
        }
        
        \Log::debug('Harga Produk Final untuk Perhitungan:', [
            'harga' => $hargaProduk,
            'formatted' => 'Rp ' . number_format($hargaProduk, 0, ',', '.'),
            'source' => is_null($sewa->produk->harga_beli) ? 'alternatif' : 'harga_beli'
        ]);
        
        // Hitung denda kerusakan
        $dendaKerusakan = 0;
        \Log::debug('Kondisi Alat:', [
            'kondisi' => $request->kondisi_alat,
            'is_baik' => $request->kondisi_alat === 'baik'
        ]);
        
        if ($request->kondisi_alat !== 'baik') {
            switch ($request->kondisi_alat) {
                case 'rusak_ringan':
                    $dendaKerusakan = $hargaProduk * 0.1;
                    \Log::debug('Denda Rusak Ringan:', [
                        'persentase' => '10%',
                        'hitung' => "$hargaProduk * 0.1 = $dendaKerusakan",
                        'formatted' => 'Rp ' . number_format($dendaKerusakan, 0, ',', '.')
                    ]);
                    break;
                case 'rusak_berat':
                    $dendaKerusakan = $hargaProduk * 0.5;
                    \Log::debug('Denda Rusak Berat:', [
                        'persentase' => '50%',
                        'hitung' => "$hargaProduk * 0.5 = $dendaKerusakan",
                        'formatted' => 'Rp ' . number_format($dendaKerusakan, 0, ',', '.')
                    ]);
                    break;
                case 'hilang':
                    $dendaKerusakan = $hargaProduk;
                    \Log::debug('Denda Hilang:', [
                        'persentase' => '100%',
                        'hitung' => "$hargaProduk * 1 = $dendaKerusakan",
                        'formatted' => 'Rp ' . number_format($dendaKerusakan, 0, ',', '.')
                    ]);
                    break;
            }
        }
        
        $totalDenda = $dendaKeterlambatan + $dendaKerusakan;
        
        \Log::debug('Ringkasan Perhitungan Final:', [
            'keterlambatan_hari' => $keterlambatan,
            'tarif_denda_per_hari' => $tarifDenda,
            'denda_keterlambatan' => $dendaKeterlambatan,
            'denda_kerusakan' => $dendaKerusakan,
            'total_denda' => $totalDenda,
            'harga_produk_digunakan' => $hargaProduk
        ]);
        
        \Log::debug('=== DEBUG CALCULATE DENDA END - SUKSES ===');
        
        return response()->json([
            'success' => true,
            'data' => [
                'keterlambatan_hari' => $keterlambatan,
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