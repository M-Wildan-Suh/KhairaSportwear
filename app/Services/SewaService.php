<?php

namespace App\Services;

use App\Models\Sewa;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pengembalian;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SewaService
{
    /**
     * Proses checkout dari keranjang
     */
    public static function checkoutFromCart($userId, $cartItems, $data = [])
    {
        return DB::transaction(function () use ($userId, $cartItems, $data) {
            // 1. Buat Transaksi
            $transaksi = Transaksi::create([
                'user_id' => $userId,
                'tipe' => Transaksi::TIPE_SEWA,
                'total_harga' => collect($cartItems)->sum('subtotal'),
                'status' => Transaksi::STATUS_MENUNGGU_PEMBAYARAN,
                'alamat_pengiriman' => $data['alamat_pengiriman'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ]);

            // 2. Buat DetailTransaksi dan Sewa untuk setiap item
            foreach ($cartItems as $item) {
                if ($item['tipe'] !== 'sewa') continue;

                // Buat DetailTransaksi
                $detail = DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk_id'],
                    'tipe_produk' => 'sewa',
                    'quantity' => $item['quantity'],
                    'harga_satuan' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                    'opsi_sewa' => $item['opsi_sewa']
                ]);

                // Buat Sewa
                $sewa = Sewa::create([
                    'transaksi_id' => $transaksi->id,
                    'detail_transaksi_id' => $detail->id,
                    'user_id' => $userId,
                    'produk_id' => $item['produk_id'],
                    'durasi' => $item['opsi_sewa']['durasi'] ?? 'harian',
                    'jumlah_hari' => $item['opsi_sewa']['jumlah_hari'] ?? 1,
                    'tanggal_mulai' => $item['opsi_sewa']['tanggal_mulai'] ?? now(),
                    'total_harga' => $item['subtotal'],
                    'status' => Sewa::STATUS_MENUNGGU_PEMBAYARAN
                ]);

                // Reserve stok
                $produk = Produk::find($item['produk_id']);
                if ($produk) {
                    $produk->stok_reserved += $item['quantity'];
                    $produk->save();
                }
            }

            return $transaksi;
        });
    }

    /**
     * Verifikasi pembayaran dan aktifkan sewa
     */
    public static function verifikasiPembayaran($transaksiId, $verifiedBy = null)
    {
        return DB::transaction(function () use ($transaksiId, $verifiedBy) {
            $transaksi = Transaksi::with(['sewa.produk', 'detailTransaksis'])->findOrFail($transaksiId);

            // Update Transaksi
            $transaksi->update([
                'status' => Transaksi::STATUS_SELESAI,
                'tanggal_pembayaran' => now(),
                'verifikator_id' => $verifiedBy,
                'tanggal_verifikasi' => now()
            ]);

            // Aktifkan semua sewa dalam transaksi
            foreach ($transaksi->sewa as $sewa) {
                // Kurangi stok_reserved, tambah stok_disewa
                if ($sewa->produk) {
                    $quantity = $sewa->getQuantity();
                    $sewa->produk->stok_reserved -= $quantity;
                    $sewa->produk->stok_disewa += $quantity;
                    $sewa->produk->save();
                }

                // Update status sewa
                $sewa->update([
                    'status' => Sewa::STATUS_AKTIF,
                    'tanggal_verifikasi' => now()
                ]);

                // Trigger event
                event(new \App\Events\SewaAktif($sewa));
            }

            return $transaksi;
        });
    }

    /**
     * Batalkan sewa (customer atau admin)
     */
    public static function batalkanSewa($sewaId, $alasan = null, $dibatalkanOleh = null)
    {
        return DB::transaction(function () use ($sewaId, $alasan, $dibatalkanOleh) {
            $sewa = Sewa::with(['produk', 'transaksi'])->findOrFail($sewaId);

            // Kembalikan stok jika sudah direserve
            if ($sewa->produk && in_array($sewa->status, [Sewa::STATUS_MENUNGGU_PEMBAYARAN, Sewa::STATUS_AKTIF])) {
                $quantity = $sewa->getQuantity();
                if ($sewa->status === Sewa::STATUS_MENUNGGU_PEMBAYARAN) {
                    $sewa->produk->stok_reserved -= $quantity;
                } else {
                    $sewa->produk->stok_disewa -= $quantity;
                }
                $sewa->produk->stok_tersedia += $quantity;
                $sewa->produk->save();
            }

            // Update sewa
            $sewa->update([
                'status' => Sewa::STATUS_DIBATALKAN,
                'alasan_pembatalan' => $alasan,
                'dibatalkan_oleh' => $dibatalkanOleh,
                'tanggal_pembatalan' => now()
            ]);

            // Jika semua sewa dalam transaksi dibatalkan, batalkan transaksi juga
            $activeSewas = $sewa->transaksi->sewa()->where('status', '!=', Sewa::STATUS_DIBATALKAN)->count();
            if ($activeSewas === 0) {
                $sewa->transaksi->update(['status' => Transaksi::STATUS_DIBATALKAN]);
            }

            return $sewa;
        });
    }

    /**
     * Cek sewa yang akan berakhir (untuk notifikasi)
     */
    public static function getSewaAkanBerakhir($days = 3)
    {
        return Sewa::aktif()
            ->where('tanggal_kembali_rencana', '<=', now()->addDays($days))
            ->where('tanggal_kembali_rencana', '>=', now())
            ->with(['user', 'produk'])
            ->get();
    }

    /**
     * Cek sewa terlambat
     */
    public static function getSewaTerlambat()
    {
        return Sewa::aktif()
            ->where('tanggal_kembali_rencana', '<', now())
            ->with(['user', 'produk']);
    }
}