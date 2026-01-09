<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            KategoriSeeder::class,
            ProdukSeeder::class,
        ]);

        // Buat 20 produk dummy tambahan
        Produk::factory(20)->create();
    }
}