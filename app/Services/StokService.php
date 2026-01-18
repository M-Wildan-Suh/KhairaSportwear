<?php

namespace App\Services;

use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class StokService
{
    /**
     * Update stok untuk sewa
     */
    public static function updateForSewa(Produk $produk, $quantity, $action)
    {
        DB::transaction(function () use ($produk, $quantity, $action) {
            switch ($action) {
                case 'reserve': // Saat masuk keranjang
                    if ($produk->stok_tersedia < $quantity) {
                        throw new \Exception('Stok tidak mencukupi.');
                    }
                    $produk->stok_tersedia -= $quantity;
                    $produk->stok_reserved += $quantity;
                    break;

                case 'confirm': // Saat pembayaran diverifikasi
                    if ($produk->stok_reserved < $quantity) {
                        throw new \Exception('Stok reserved tidak mencukupi.');
                    }
                    $produk->stok_reserved -= $quantity;
                    $produk->stok_disewa += $quantity;
                    break;

                case 'cancel_reserve': // Saat dibatalkan sebelum bayar
                    $produk->stok_tersedia += $quantity;
                    $produk->stok_reserved -= $quantity;
                    break;

                case 'return': // Saat dikembalikan
                    $produk->stok_disewa -= $quantity;
                    $produk->stok_tersedia += $quantity;
                    break;

                case 'cancel_active': // Saat dibatalkan setelah aktif
                    $produk->stok_disewa -= $quantity;
                    $produk->stok_tersedia += $quantity;
                    break;
            }

            $produk->save();
        });
    }

    /**
     * Cek ketersediaan stok untuk sewa
     */
    public static function checkAvailability($produkId, $quantity, $tanggalMulai, $tanggalSelesai)
    {
        $produk = Produk::findOrFail($produkId);
        
        // Cek stok tersedia
        if ($produk->stok_tersedia < $quantity) {
            return false;
        }

        // Cek konflik jadwal (jika diperlukan)
        // Anda bisa tambahkan logika untuk cek sewa aktif di tanggal yang sama
        
        return true;
    }
}