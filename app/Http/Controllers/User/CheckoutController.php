<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\Sewa;
use App\Models\Produk;
use App\Models\Varian;
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
    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$request->has('items')) {
            return redirect()->route('user.keranjang.index')
                ->with('error', 'Pilih minimal satu item untuk checkout.');
        }

        $ids = explode(',', $request->items);

        $cartItems = Keranjang::where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->with('produk')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.keranjang.index')
                ->with('error', 'Item checkout tidak ditemukan.');
        }

        $sewaItems = $cartItems->where('tipe', 'sewa');
        $beliItems = $cartItems->where('tipe', 'jual');

        $subtotal = $cartItems->sum('subtotal');
        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;

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
        $user = Auth::user();

        if (!$request->items) {
            return back()->with('error', 'Item checkout tidak ditemukan.');
        }

        $ids = explode(',', $request->items);

        $cartItems = Keranjang::where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->with('produk')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        DB::beginTransaction();
        try {

            $transaksi = Transaksi::create([
                'user_id' => $user->id,
                'total'   => $cartItems->sum('subtotal'),
                'status'  => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $produk = Produk::lockForUpdate()->find($item->produk_id);

                if ($produk->stok < $item->quantity) {
                    throw new \Exception('Stok produk habis');
                }

                // KURANGI STOK PRODUK
                $produk->decrement('stok', $item->quantity);

                // JIKA ADA VARIAN
                if ($item->bundle_id) {
                    $varian = Varian::lockForUpdate()->find($item->bundle_id);

                    if (!$varian || $varian->stok < $item->quantity) {
                        throw new \Exception('Stok varian habis');
                    }

                    // KURANGI STOK VARIAN
                    $varian->decrement('stok', $item->quantity);
                }
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id'    => $item->produk_id,
                    'quantity'     => $item->quantity,
                    'varian_id'     => $item->bundle_id,
                    'harga'        => $item->harga,
                    'subtotal'     => $item->subtotal,
                ]);
            }

            // 🔥 INI YANG BENAR
            Keranjang::whereIn('id', $ids)->delete();

            DB::commit();

            return redirect()->route('user.transaksi.success');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
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
                'status' => 'tidak aktif',
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
