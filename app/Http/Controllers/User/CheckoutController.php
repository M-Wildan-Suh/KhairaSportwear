<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\Sewa;
use App\Models\DetailTransaksi;
use App\Services\SewaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman checkout untuk transaksi (untuk route user.transaksi.create)
     * Ini akan menangani baik sewa maupun beli
     */
    public function create()
    {
        $user = Auth::user();
        
        // Ambil semua item dari keranjang (baik sewa maupun beli)
        $cartItems = Keranjang::where('user_id', $user->id)
            ->with('produk')
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('user.keranjang.index')
                ->with('error', 'Keranjang Anda kosong.');
        }
        
        // Pisahkan item sewa dan beli
        $sewaItems = $cartItems->where('tipe', 'sewa');
        $beliItems = $cartItems->where('tipe', 'jual');
        
        // Validasi untuk item sewa
        $validationErrors = [];
        foreach ($sewaItems as $item) {
            // Validasi stok
            if ($item->produk->stok_tersedia < $item->quantity) {
                $validationErrors[] = "Stok {$item->produk->nama} tidak mencukupi untuk disewa.";
            }
            
            // Validasi tanggal mulai sewa
            if (isset($item->opsi_sewa['tanggal_mulai'])) {
                $tanggalMulai = Carbon::parse($item->opsi_sewa['tanggal_mulai']);
                if ($tanggalMulai->isPast() && !$tanggalMulai->isToday()) {
                    $validationErrors[] = "Tanggal mulai sewa untuk {$item->produk->nama} tidak valid.";
                }
            }
        }
        
        // Validasi untuk item beli
        foreach ($beliItems as $item) {
            if ($item->produk->stok_tersedia < $item->quantity) {
                $validationErrors[] = "Stok {$item->produk->nama} tidak mencukupi untuk dibeli.";
            }
        }
        
        if (!empty($validationErrors)) {
            return redirect()->route('user.keranjang.index')
                ->withErrors(['validation' => $validationErrors]);
        }
        
        // Hitung total
        $subtotal = $cartItems->sum('subtotal');
        $tax = $subtotal * 0.11; // PPN 11%
        $total = $subtotal + $tax;
        
        // Alamat pengiriman user
        $alamat = $user->alamat ?? '';
        
        return view('user.transaksi.create', compact(
            'cartItems',
            'sewaItems',
            'beliItems',
            'subtotal',
            'tax',
            'total',
            'alamat'
        ));
    }
    
    /**
     * Proses checkout (untuk route user.transaksi.store)
     * Menangani transaksi campuran (sewa + beli) atau hanya salah satu
     */
    public function store(Request $request)
    {
        $request->validate([
            'alamat_pengiriman' => 'required_if:jenis_pengiriman,delivery|string|max:255',
            'jenis_pengiriman' => 'required|in:pickup,delivery',
            'catatan' => 'nullable|string|max:500',
            'metode_pembayaran' => 'required|in:transfer,tunai,qris',
            'bukti_pembayaran' => 'nullable|image|max:2048',
        ]);
        
        $user = Auth::user();
        
        // Ambil semua item dari keranjang
        $cartItems = Keranjang::where('user_id', $user->id)
            ->with('produk')
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('user.keranjang.index')
                ->with('error', 'Keranjang Anda kosong.');
        }
        
        // Pisahkan item sewa dan beli
        $sewaItems = $cartItems->where('tipe', 'sewa');
        $beliItems = $cartItems->where('tipe', 'jual');
        
        try {
            DB::beginTransaction();
            
            // Tentukan tipe transaksi
            $tipeTransaksi = '';
            if ($sewaItems->isNotEmpty() && $beliItems->isNotEmpty()) {
                $tipeTransaksi = 'penjualan_dan_sewa';
            } elseif ($sewaItems->isNotEmpty()) {
                $tipeTransaksi = 'sewa';
            } else {
                $tipeTransaksi = 'penjualan';
            }
            
            // Buat transaksi
            $transaksi = Transaksi::create([
                'user_id' => $user->id,
                'kode_transaksi' => Transaksi::generateKodeTransaksi(),
                'tipe' => $tipeTransaksi,
                'total_harga' => $cartItems->sum('subtotal'),
                'total_bayar' => $cartItems->sum('subtotal') * 1.11, // termasuk PPN
                'status' => 'menunggu_pembayaran',
                'metode_pembayaran' => $request->metode_pembayaran,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'catatan' => $request->catatan,
            ]);
            
            // Upload bukti pembayaran jika ada
            if ($request->hasFile('bukti_pembayaran')) {
                $path = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
                $transaksi->update([
                    'bukti_pembayaran' => $path,
                    'status' => 'menunggu_konfirmasi'
                ]);
            }
            
            // Proses setiap item di keranjang
            foreach ($cartItems as $cartItem) {
                // Buat detail transaksi
                $detailTransaksi = DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $cartItem->produk_id,
                    'tipe_produk' => $cartItem->tipe,
                    'quantity' => $cartItem->quantity,
                    'harga_satuan' => $cartItem->harga,
                    'subtotal' => $cartItem->subtotal,
                    'opsi_sewa' => $cartItem->tipe === 'sewa' ? $cartItem->opsi_sewa : null
                ]);
                
                // Jika item adalah sewa, buat record Sewa
                if ($cartItem->tipe === 'sewa') {
                    Sewa::create([
                        'transaksi_id' => $transaksi->id,
                        'detail_transaksi_id' => $detailTransaksi->id,
                        'user_id' => $user->id,
                        'produk_id' => $cartItem->produk_id,
                        'kode_sewa' => Sewa::generateKodeSewa(),
                        'durasi' => $cartItem->opsi_sewa['durasi'] ?? 'harian',
                        'jumlah_hari' => $cartItem->opsi_sewa['jumlah_hari'] ?? 1,
                        'tanggal_mulai' => $cartItem->opsi_sewa['tanggal_mulai'] ?? now(),
                        'tanggal_selesai' => Carbon::parse($cartItem->opsi_sewa['tanggal_mulai'] ?? now())
                            ->addDays($cartItem->opsi_sewa['jumlah_hari'] ?? 1),
                        'tanggal_kembali_rencana' => Carbon::parse($cartItem->opsi_sewa['tanggal_mulai'] ?? now())
                            ->addDays($cartItem->opsi_sewa['jumlah_hari'] ?? 1),
                        'status' => 'menunggu_pembayaran',
                        'total_harga' => $cartItem->subtotal,
                    ]);
                    
                    // Kurangi stok reserved (stok sudah direserve saat masuk keranjang)
                    $cartItem->produk->stok_reserved -= $cartItem->quantity;
                    $cartItem->produk->save();
                } else {
                    // Untuk item beli, langsung kurangi stok
                    $cartItem->produk->stok_tersedia -= $cartItem->quantity;
                    $cartItem->produk->save();
                }
            }
            
            // Hapus semua item dari keranjang
            Keranjang::where('user_id', $user->id)->delete();
            
            DB::commit();
            
            return redirect()->route('user.transaksi.show', $transaksi->id)
                ->with('success', 'Checkout berhasil! Silakan lakukan pembayaran.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Checkout gagal: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Proses checkout sewa langsung dari halaman produk (tanpa keranjang)
     */
    public function directSewa(Request $request, $produkId)
    {
        $request->validate([
            'durasi' => 'required|in:harian,mingguan,bulanan',
            'jumlah_hari' => 'required|integer|min:1|max:365',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'quantity' => 'required|integer|min:1',
            'alamat_pengiriman' => 'required_if:jenis_pengiriman,delivery',
            'jenis_pengiriman' => 'required|in:pickup,delivery',
            'catatan' => 'nullable|string',
            'metode_pembayaran' => 'required|in:transfer,tunai,qris',
        ]);
        
        $user = Auth::user();
        
        try {
            DB::beginTransaction();
            
            $produk = \App\Models\Produk::findOrFail($produkId);
            
            // Validasi stok
            if ($produk->stok_tersedia < $request->quantity) {
                return redirect()->back()
                    ->with('error', 'Stok tidak mencukupi.')
                    ->withInput();
            }
            
            // Hitung harga sewa
            $hargaPerHari = $produk->getHargaSewa($request->durasi);
            $subtotal = $hargaPerHari * $request->jumlah_hari * $request->quantity;
            $total = $subtotal * 1.11; // termasuk PPN
            
            // Buat transaksi
            $transaksi = Transaksi::create([
                'user_id' => $user->id,
                'kode_transaksi' => Transaksi::generateKodeTransaksi(),
                'tipe' => 'sewa',
                'total_harga' => $subtotal,
                'total_bayar' => $total,
                'status' => 'menunggu_pembayaran',
                'metode_pembayaran' => $request->metode_pembayaran,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'catatan' => $request->catatan,
            ]);
            
            // Buat detail transaksi
            $detailTransaksi = DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $produkId,
                'tipe_produk' => 'sewa',
                'quantity' => $request->quantity,
                'harga_satuan' => $hargaPerHari,
                'subtotal' => $subtotal,
                'opsi_sewa' => [
                    'durasi' => $request->durasi,
                    'jumlah_hari' => $request->jumlah_hari,
                    'tanggal_mulai' => $request->tanggal_mulai,
                ]
            ]);
            
            // Buat record sewa
            $sewa = Sewa::create([
                'transaksi_id' => $transaksi->id,
                'detail_transaksi_id' => $detailTransaksi->id,
                'user_id' => $user->id,
                'produk_id' => $produkId,
                'kode_sewa' => Sewa::generateKodeSewa(),
                'durasi' => $request->durasi,
                'jumlah_hari' => $request->jumlah_hari,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => Carbon::parse($request->tanggal_mulai)->addDays($request->jumlah_hari),
                'tanggal_kembali_rencana' => Carbon::parse($request->tanggal_mulai)->addDays($request->jumlah_hari),
                'status' => 'menunggu_pembayaran',
                'total_harga' => $subtotal,
            ]);
            
            // Kurangi stok
            $produk->stok_tersedia -= $request->quantity;
            $produk->stok_reserved += $request->quantity;
            $produk->save();
            
            DB::commit();
            
            return redirect()->route('user.transaksi.show', $transaksi->id)
                ->with('success', 'Sewa berhasil dibuat. Silakan lakukan pembayaran.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal membuat sewa: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Upload bukti pembayaran untuk transaksi (untuk route user.transaksi.upload-bukti)
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|max:2048',
            'nama_bank' => 'required_if:metode_pembayaran,transfer',
            'no_rekening' => 'required_if:metode_pembayaran,transfer',
            'atas_nama' => 'required_if:metode_pembayaran,transfer',
        ]);
        
        $transaksi = Transaksi::where('user_id', Auth::id())
            ->findOrFail($id);
        
        try {
            // Upload bukti pembayaran
            $path = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
            
            $updateData = [
                'bukti_pembayaran' => $path,
                'status' => 'menunggu_konfirmasi',
                'tanggal_pembayaran' => now(),
            ];
            
            // Tambahkan data bank jika transfer
            if ($transaksi->metode_pembayaran === 'transfer') {
                $updateData['nama_bank'] = $request->nama_bank;
                $updateData['no_rekening'] = $request->no_rekening;
                $updateData['atas_nama'] = $request->atas_nama;
            }
            
            $transaksi->update($updateData);
            
            // Update status sewa jika transaksi adalah sewa
            if ($transaksi->tipe === 'sewa' || $transaksi->tipe === 'penjualan_dan_sewa') {
                Sewa::where('transaksi_id', $transaksi->id)
                    ->where('status', 'menunggu_pembayaran')
                    ->update(['status' => 'menunggu_konfirmasi']);
            }
            
            return redirect()->route('user.transaksi.show', $transaksi->id)
                ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal upload bukti pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }
}