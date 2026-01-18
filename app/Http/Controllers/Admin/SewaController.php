<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sewa;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Denda;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SewaController extends Controller
{
    public function index(Request $request)
    {
        $query = Sewa::with(['user', 'produk', 'transaksi', 'pengembalian.denda'])
            ->latest();
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_sewa', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                  })
                  ->orWhereHas('produk', function($q) use ($request) {
                      $q->where('nama', 'like', "%{$request->search}%");
                  });
            });
        }
        
        // Filter status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter periode
        if ($request->has('periode')) {
            $today = Carbon::today();
            
            switch ($request->periode) {
                case 'today':
                    $query->whereDate('created_at', $today);
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [
                        $today->copy()->startOfMonth(),
                        $today->copy()->endOfMonth()
                    ]);
                    break;
            }
        }
        
        $sewas = $query->paginate(15);
        
        // Statistics - gunakan konstanta dari model
        $totalSewa = Sewa::count();
        $aktifCount = Sewa::where('status', Sewa::STATUS_AKTIF)->count();
        $terlambatCount = Sewa::where('status', 'aktif')
            ->where('tanggal_kembali_rencana', '<', Carbon::today())
            ->count();
        $totalPendapatan = Sewa::where('status', '!=', Sewa::STATUS_DIBATALKAN)
            ->sum('total_harga');
        
        return view('admin.sewa.index', compact(
            'sewas', 
            'totalSewa', 
            'aktifCount', 
            'terlambatCount', 
            'totalPendapatan'
        ));
    }
    
    public function aktif()
    {
        $sewas = Sewa::with(['user', 'produk', 'transaksi'])
            ->where('status', Sewa::STATUS_AKTIF)
            ->orderBy('tanggal_kembali_rencana')
            ->paginate(15);
            
        return view('admin.sewa.aktif', compact('sewas'));
    }
    
    public function terlambat()
    {
        $sewas = Sewa::with(['user', 'produk', 'transaksi'])
            ->where('status', Sewa::STATUS_AKTIF)
            ->where('tanggal_kembali_rencana', '<', Carbon::today())
            ->orderBy('tanggal_kembali_rencana')
            ->paginate(15);
            
        return view('admin.sewa.terlambat', compact('sewas'));
    }
    
    public function show(Sewa $sewa)
    {
        // Load relations yang diperlukan
        $sewa->load([
            'user', 
            'produk', 
            'transaksi', 
            'transaksi.detailTransaksis', // tambahkan ini untuk items
            'pengembalian',
            'pengembalian.denda'
        ]);
        
        return view('admin.sewa.show', compact('sewa'));
    }
    
    public function updateStatus(Request $request, Sewa $sewa)
    {
        $request->validate([
            'status' => 'required|in:aktif,selesai,dibatalkan',
            'catatan' => 'nullable|string|max:500'
        ]);
        
        DB::beginTransaction();
        
        try {
            $oldStatus = $sewa->status;
            
            switch ($request->status) {
                case 'aktif':
                    // Aktifkan sewa (konfirmasi pembayaran)
                    if ($oldStatus !== Sewa::STATUS_MENUNGGU_KONFIRMASI) {
                        throw new \Exception('Hanya sewa menunggu konfirmasi yang dapat diaktifkan.');
                    }
                    
                    $sewa->aktifkan();
                    $sewa->catatan = $request->catatan;
                    break;
                    
                case 'selesai':
                    // Selesaikan sewa
                    if ($oldStatus !== Sewa::STATUS_AKTIF) {
                        throw new \Exception('Hanya sewa aktif yang dapat diselesaikan.');
                    }
                    
                    $sewa->status = Sewa::STATUS_SELESAI;
                    $sewa->tanggal_kembali_aktual = Carbon::now();
                    $sewa->catatan = $request->catatan;
                    
                    // Kembalikan stok
                    if ($sewa->produk) {
                        // Cari quantity dari detail transaksi
                        $quantity = 1;
                        if ($sewa->transaksi && $sewa->transaksi->detailTransaksis) {
                            $detail = $sewa->transaksi->detailTransaksis
                                ->where('produk_id', $sewa->produk_id)
                                ->first();
                            $quantity = $detail->quantity ?? 1;
                        }
                        
                        $sewa->produk->updateStokSewa($quantity, 'masuk');
                    }
                    break;
                    
                case 'dibatalkan':
                    // Batalkan sewa
                    $sewa->batalkan($request->catatan, auth()->id());
                    break;
            }
            
            $sewa->save();
            
            DB::commit();
            
            return redirect()->route('admin.sewa.show', $sewa->id)
                ->with('success', 'Status sewa berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
    
    public function pengembalian(Request $request, Sewa $sewa)
    {
        $request->validate([
            'tanggal_kembali' => 'required|date',
            'kondisi_barang' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan' => 'nullable|string|max:500',
            'denda' => 'nullable|numeric|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Validasi status
            if ($sewa->status !== Sewa::STATUS_AKTIF) {
                throw new \Exception('Hanya sewa aktif yang dapat diproses pengembaliannya.');
            }
            
            // Proses pengembalian dengan method dari model
            $pengembalian = $sewa->prosesPengembalian(
                $request->tanggal_kembali,
                $request->kondisi_barang,
                $request->catatan
            );
            
            // Jika ada input denda manual, update
            if ($request->filled('denda')) {
                $sewa->denda = $request->denda;
                $sewa->save();
                
                // Update denda record jika ada
                if ($pengembalian->denda) {
                    $pengembalian->denda->update([
                        'jumlah_denda' => $request->denda,
                        'keterangan' => 'Denda manual: ' . ($request->catatan ?? '-')
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.sewa.show', $sewa->id)
                ->with('success', 'Pengembalian berhasil diproses.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error proses pengembalian: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
    
    public function destroy(Sewa $sewa)
    {
        // Hanya sewa dengan status tertentu yang bisa dihapus
        $allowedStatus = [
            Sewa::STATUS_DIBATALKAN,
            Sewa::STATUS_EXPIRED
        ];
        
        if (!in_array($sewa->status, $allowedStatus)) {
            return redirect()->back()
                ->with('error', 'Sewa yang aktif/selesai tidak dapat dihapus.');
        }
        
        DB::beginTransaction();
        
        try {
            // Hapus denda terkait jika ada
            if ($sewa->pengembalian && $sewa->pengembalian->denda) {
                $sewa->pengembalian->denda->delete();
            }
            
            // Hapus pengembalian jika ada
            if ($sewa->pengembalian) {
                $sewa->pengembalian->delete();
            }
            
            // Hapus sewa
            $sewa->delete();
            
            DB::commit();
            
            return redirect()->route('admin.sewa.index')
                ->with('success', 'Sewa berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus sewa: ' . $e->getMessage());
        }
    }
    
    public function verifikasiPengembalian(Sewa $sewa)
    {
        DB::beginTransaction();
        
        try {
            // Validasi status
            if ($sewa->status !== Sewa::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN) {
                throw new \Exception('Hanya sewa menunggu verifikasi yang dapat diverifikasi.');
            }
            
            // Verifikasi pengembalian
            $sewa->verifikasiPengembalian(auth()->id());
            
            DB::commit();
            
            return redirect()->route('admin.sewa.show', $sewa->id)
                ->with('success', 'Pengembalian berhasil diverifikasi.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal verifikasi pengembalian: ' . $e->getMessage());
        }
    }
}