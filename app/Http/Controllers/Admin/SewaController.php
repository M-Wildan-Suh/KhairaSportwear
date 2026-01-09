<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sewa;
use App\Models\Konfigurasi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SewaController extends Controller
{
    public function index(Request $request)
    {
        $query = Sewa::with(['user', 'produk', 'pengembalian.denda'])
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
        
        // Statistics
        $totalSewa = Sewa::count();
        $aktifCount = Sewa::where('status', 'aktif')->count();
        $terlambatCount = Sewa::where('status', 'terlambat')->count();
        $totalPendapatan = Sewa::where('status', '!=', 'dibatalkan')->sum('total_harga');
        
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
        // Update status sewa yang terlambat
        $this->updateTerlambatSewa();
        
        $sewas = Sewa::with(['user', 'produk'])
            ->where('status', 'aktif')
            ->orderBy('tanggal_kembali_rencana')
            ->paginate(15);
            
        return view('admin.sewa.aktif', compact('sewas'));
    }
    
    public function show(Sewa $sewa)
    {
        $sewa->load(['user', 'produk', 'transaksi', 'pengembalian.denda']);
        
        // Hitung denda jika belum ada
        if (!$sewa->denda && $sewa->hitungKeterlambatan() > 0) {
            $denda = $sewa->hitungDenda();
        }
        
        return view('admin.sewa.show', compact('sewa'));
    }
    
    public function updateStatus(Request $request, Sewa $sewa)
    {
        $request->validate([
            'status' => 'required|in:aktif,selesai,terlambat,dibatalkan'
        ]);
        
        $oldStatus = $sewa->status;
        $sewa->status = $request->status;
        
        // Jika status diubah menjadi selesai, set tanggal kembali aktual
        if ($request->status == 'selesai' && !$sewa->tanggal_kembali_aktual) {
            $sewa->tanggal_kembali_aktual = Carbon::now();
            
            // Kembalikan stok produk
            if ($sewa->produk) {
                $sewa->produk->updateStokSewa($sewa->jumlah_hari, 'masuk');
            }
        }
        
        // Jika dibatalkan, kembalikan stok
        if ($request->status == 'dibatalkan' && $sewa->produk) {
            $sewa->produk->updateStokSewa($sewa->jumlah_hari, 'masuk');
        }
        
        $sewa->save();
        
        return redirect()->back()->with('success', 'Status sewa berhasil diperbarui.');
    }
    
    public function pengembalian(Request $request, Sewa $sewa)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan' => 'nullable|string|max:500'
        ]);
        
        // Update sewa
        $sewa->tanggal_kembali_aktual = $request->tanggal_kembali_aktual;
        $sewa->status = 'selesai';
        $sewa->save();
        
        // Hitung denda jika terlambat
        $keterlambatan = $sewa->hitungKeterlambatan();
        if ($keterlambatan > 0) {
            $denda = $sewa->hitungDenda();
            $sewa->denda = $denda;
            $sewa->save();
        }
        
        // Kembalikan stok produk
        if ($sewa->produk) {
            $sewa->produk->updateStokSewa($sewa->jumlah_hari, 'masuk');
        }
        
        return redirect()->route('admin.sewa.show', $sewa->id)
            ->with('success', 'Pengembalian berhasil dicatat.');
    }
    
    public function destroy(Sewa $sewa)
    {
        // Hanya sewa dengan status tertentu yang bisa dihapus
        if (!in_array($sewa->status, ['dibatalkan'])) {
            return redirect()->back()
                ->with('error', 'Sewa yang aktif/selesai tidak dapat dihapus.');
        }
        
        $sewa->delete();
        
        return redirect()->route('admin.sewa.index')
            ->with('success', 'Sewa berhasil dihapus.');
    }
    
    private function updateTerlambatSewa()
    {
        $sewas = Sewa::where('status', 'aktif')
            ->whereDate('tanggal_kembali_rencana', '<', Carbon::today())
            ->get();
            
        foreach ($sewas as $sewa) {
            $sewa->status = 'terlambat';
            $sewa->save();
        }
    }
}