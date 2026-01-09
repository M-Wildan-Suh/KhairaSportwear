<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\Kategori;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriIds = Kategori::pluck('id')->toArray();

        if (count($kategoriIds) < 5) {
            $this->command->error('Kategori belum cukup. Tambahkan minimal 5 kategori sebelum seeding produk.');
            return;
        }

        $produks = [
            [
                'nama' => 'Sepatu Running Nike Air Max',
                'slug' => 'sepatu-running-nike-air-max',
                'kategori_id' => $kategoriIds[0],
                'tipe' => 'jual',
                'harga_beli' => 1500000,
                'stok_total' => 10,
                'stok_tersedia' => 10,
            ],
            [
                'nama' => 'Treadmill Elektrik',
                'slug' => 'treadmill-elektrik',
                'kategori_id' => $kategoriIds[2],
                'tipe' => 'sewa',
                'harga_sewa_harian' => 50000,
                'harga_sewa_mingguan' => 300000,
                'harga_sewa_bulanan' => 1000000,
                'stok_total' => 5,
                'stok_tersedia' => 5,
            ],
            [
                'nama' => 'Set Raket Bulutangkis Yonex',
                'slug' => 'set-raket-bulutangkis-yonex',
                'kategori_id' => $kategoriIds[4],
                'tipe' => 'both',
                'harga_beli' => 800000,
                'harga_sewa_harian' => 20000,
                'harga_sewa_mingguan' => 100000,
                'harga_sewa_bulanan' => 350000,
                'stok_total' => 15,
                'stok_tersedia' => 15,
            ],
            [
                'nama' => 'Bola Basket Spalding',
                'slug' => 'bola-basket-spalding',
                'kategori_id' => $kategoriIds[3],
                'tipe' => 'both',
                'harga_beli' => 350000,
                'harga_sewa_harian' => 15000,
                'harga_sewa_mingguan' => 75000,
                'harga_sewa_bulanan' => 250000,
                'stok_total' => 20,
                'stok_tersedia' => 20,
            ],
            [
                'nama' => 'Sepeda Statis',
                'slug' => 'sepeda-statis',
                'kategori_id' => $kategoriIds[2],
                'tipe' => 'sewa',
                'harga_sewa_harian' => 40000,
                'harga_sewa_mingguan' => 250000,
                'harga_sewa_bulanan' => 800000,
                'stok_total' => 8,
                'stok_tersedia' => 8,
            ],
        ];

        foreach ($produks as $produk) {
            // Gunakan firstOrCreate supaya aman dijalankan berkali-kali
            Produk::firstOrCreate(
                ['slug' => $produk['slug']], // unik berdasarkan slug
                $produk
            );
        }
    }
}
