<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\Sewa;
use App\Models\Konfigurasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = auth()->user()->transaksis()
            ->with(['detailTransaksis.produk'])
            ->latest()
            ->paginate(10);
        
        return view('user.transaksi.index', compact('transaksis'));
    }
    
    public function create()
    {
        $user = auth()->user();
        $keranjangs = $user->keranjangs()
            ->with('produk')
            ->get();
        
        if ($keranjangs->isEmpty()) {
            return redirect()->route('user.keranjang.index')
                ->with('error', 'Keranjang Anda kosong. Silakan tambahkan produk terlebih dahulu.');
        }
        
        // Validate stock before checkout
        foreach ($keranjangs as $item) {
            if ($item->produk->stok_tersedia < $item->quantity) {
                return redirect()->route('user.keranjang.index')
                    ->with('error', "Stok {$item->produk->nama} tidak mencukupi.");
            }
        }
        
        // Calculate totals
        $subtotal = $keranjangs->sum('subtotal');
        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;
        
        // Get bank transfer info from config
        $bankInfo = Konfigurasi::getValue('bank_transfer', []);
        $noRekening = Konfigurasi::getValue('no_rekening_admin');
        $namaRekening = Konfigurasi::getValue('nama_rekening_admin');
        
        return view('user.transaksi.create', compact(
            'keranjangs', 'subtotal', 'tax', 'total',
            'bankInfo', 'noRekening', 'namaRekening'
        ));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:transfer_bank,tunai,qris',
            'nama_bank' => 'required_if:metode_pembayaran,transfer_bank',
            'no_rekening' => 'required_if:metode_pembayaran,transfer_bank',
            'atas_nama' => 'required_if:metode_pembayaran,transfer_bank',
            'catatan' => 'nullable|string|max:500',
            'alamat_pengiriman' => 'required_if:tipe,penjualan|string|max:1000'
        ]);
        
        $user = auth()->user();
        $keranjangs = $user->keranjangs()->with('produk')->get();
        
        if ($keranjangs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang Anda kosong.'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            // Determine transaction type based on cart items
            $hasJual = $keranjangs->where('tipe', 'jual')->isNotEmpty();
            $hasSewa = $keranjangs->where('tipe', 'sewa')->isNotEmpty();
            
            if ($hasJual && $hasSewa) {
                // Create separate transactions for jual and sewa
                $this->createSeparateTransactions($user, $keranjangs, $request);
            } else {
                // Create single transaction
                $this->createSingleTransaction($user, $keranjangs, $request);
            }
            
            // Clear cart
            $user->keranjangs()->delete();
            
            DB::commit();
            
            // Create notification
            \App\Models\Notifikasi::createNotifikasi(
                $user->id,
                'Transaksi Berhasil',
                'Transaksi Anda berhasil dibuat. Silakan upload bukti pembayaran.',
                'transaksi'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat.',
                'redirect' => route('user.transaksi.index')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function createSingleTransaction($user, $keranjangs, $request)
    {
        $tipe = $keranjangs->first()->tipe === 'jual' ? 'penjualan' : 'penyewaan';
        
        $subtotal = $keranjangs->sum('subtotal');
        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;
        
        // Create transaction
        $transaksi = Transaksi::create([
            'kode_transaksi' => Transaksi::generateKodeTransaksi(),
            'user_id' => $user->id,
            'tipe' => $tipe,
            'total_harga' => $subtotal,
            'diskon' => 0,
            'total_bayar' => $total,
            'status' => 'pending',
            'metode_pembayaran' => $request->metode_pembayaran,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
            'catatan' => $request->catatan,
            'alamat_pengiriman' => $request->alamat_pengiriman
        ]);
        
        // Create detail transactions and handle stock
        foreach ($keranjangs as $item) {
            // Create detail transaction
            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $item->produk_id,
                'tipe_produk' => $item->tipe,
                'quantity' => $item->quantity,
                'harga_satuan' => $item->harga,
                'subtotal' => $item->subtotal,
                'opsi_sewa' => $item->opsi_sewa
            ]);
            
            // Update product stock
            if ($item->tipe === 'jual') {
                $item->produk->updateStok($item->quantity, 'keluar');
            } else {
                // For sewa, create rental record
                $this->createSewaRecord($transaksi, $item);
                $item->produk->updateStokSewa($item->quantity, 'keluar');
            }
        }
        
        return $transaksi;
    }
    
    private function createSeparateTransactions($user, $keranjangs, $request)
    {
        $jualItems = $keranjangs->where('tipe', 'jual');
        $sewaItems = $keranjangs->where('tipe', 'sewa');
        
        // Create penjualan transaction
        if ($jualItems->isNotEmpty()) {
            $this->createTransactionForItems($user, $jualItems, 'penjualan', $request);
        }
        
        // Create penyewaan transaction
        if ($sewaItems->isNotEmpty()) {
            $this->createTransactionForItems($user, $sewaItems, 'penyewaan', $request);
        }
    }
    
    private function createTransactionForItems($user, $items, $tipe, $request)
    {
        $subtotal = $items->sum('subtotal');
        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;
        
        $transaksi = Transaksi::create([
            'kode_transaksi' => Transaksi::generateKodeTransaksi(),
            'user_id' => $user->id,
            'tipe' => $tipe,
            'total_harga' => $subtotal,
            'diskon' => 0,
            'total_bayar' => $total,
            'status' => 'pending',
            'metode_pembayaran' => $request->metode_pembayaran,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
            'catatan' => $request->catatan,
            'alamat_pengiriman' => $tipe === 'penjualan' ? $request->alamat_pengiriman : null
        ]);
        
        foreach ($items as $item) {
            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $item->produk_id,
                'tipe_produk' => $item->tipe,
                'quantity' => $item->quantity,
                'harga_satuan' => $item->harga,
                'subtotal' => $item->subtotal,
                'opsi_sewa' => $item->opsi_sewa
            ]);
            
            if ($tipe === 'penjualan') {
                $item->produk->updateStok($item->quantity, 'keluar');
            } else {
                $this->createSewaRecord($transaksi, $item);
                $item->produk->updateStokSewa($item->quantity, 'keluar');
            }
        }
        
        return $transaksi;
    }
    
    private function createSewaRecord($transaksi, $item)
    {
        $opsi = $item->opsi_sewa;
        $jumlahHari = $opsi['jumlah_hari'] ?? 1;
        $tanggalMulai = Carbon::parse($opsi['tanggal_mulai']);
        $tanggalSelesai = $tanggalMulai->copy()->addDays($jumlahHari);
        
        return Sewa::create([
            'transaksi_id' => $transaksi->id,
            'user_id' => $transaksi->user_id,
            'produk_id' => $item->produk_id,
            'kode_sewa' => Sewa::generateKodeSewa(),
            'durasi' => $opsi['durasi'] ?? 'harian',
            'jumlah_hari' => $jumlahHari,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'tanggal_kembali_rencana' => $tanggalSelesai,
            'total_harga' => $item->subtotal,
            'status' => 'aktif'
        ]);
    }
    
    public function show($id)
    {
        $transaksi = auth()->user()->transaksis()
            ->with(['detailTransaksis.produk', 'sewa.produk'])
            ->findOrFail($id);
        
        return view('user.transaksi.show', compact('transaksi'));
    }
    
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        
        $transaksi = auth()->user()->transaksis()->findOrFail($id);
        
        if ($transaksi->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya transaksi pending yang dapat diupload bukti pembayaran.'
            ], 400);
        }
        
        // Upload image
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('bukti-pembayaran', $filename, 'public');
            
            $transaksi->bukti_pembayaran = $filename;
            $transaksi->status = 'diproses';
            $transaksi->save();
            
            // Create notification
            \App\Models\Notifikasi::createNotifikasi(
                $transaksi->user_id,
                'Bukti Pembayaran Diupload',
                'Bukti pembayaran untuk transaksi ' . $transaksi->kode_transaksi . ' telah diupload.',
                'transaksi',
                route('user.transaksi.show', $transaksi->id)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload.',
                'bukti_url' => $transaksi->bukti_pembayaran_url
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupload bukti pembayaran.'
        ], 400);
    }
    
    public function cancel($id)
    {
        $transaksi = auth()->user()->transaksis()->findOrFail($id);
        
        if (!in_array($transaksi->status, ['pending', 'diproses'])) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak dapat dibatalkan.'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($transaksi->detailTransaksis as $detail) {
                if ($detail->tipe_produk === 'jual') {
                    $detail->produk->updateStok($detail->quantity, 'masuk');
                } else {
                    $detail->produk->updateStokSewa($detail->quantity, 'masuk');
                }
            }
            
            // Cancel any rental records
            if ($transaksi->sewa) {
                $transaksi->sewa->update(['status' => 'dibatalkan']);
            }
            
            // Update transaction status
            $transaksi->status = 'dibatalkan';
            $transaksi->save();
            
            DB::commit();
            
            // Create notification
            \App\Models\Notifikasi::createNotifikasi(
                $transaksi->user_id,
                'Transaksi Dibatalkan',
                'Transaksi ' . $transaksi->kode_transaksi . ' telah dibatalkan.',
                'warning',
                route('user.transaksi.show', $transaksi->id)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan.'
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