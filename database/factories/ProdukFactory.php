<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kategori;

class ProdukFactory extends Factory
{
    public function definition(): array
    {
        $kategori = Kategori::inRandomOrder()->first() ?? Kategori::factory()->create();
        $tipe = $this->faker->randomElement(['jual', 'sewa', 'both']);
        
        $hargaBeli = $tipe !== 'sewa' ? $this->faker->numberBetween(100000, 5000000) : null;
        $hargaSewa = $tipe !== 'jual' ? $this->faker->numberBetween(10000, 100000) : null;

        return [
            'kategori_id' => $kategori->id,
            'nama' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'deskripsi' => $this->faker->paragraph(),
            'tipe' => $tipe,
            'harga_beli' => $hargaBeli,
            'harga_sewa_harian' => $hargaSewa,
            'harga_sewa_mingguan' => $hargaSewa ? $hargaSewa * 5 : null,
            'harga_sewa_bulanan' => $hargaSewa ? $hargaSewa * 20 : null,
            'stok_total' => $this->faker->numberBetween(1, 50),
            'stok_tersedia' => function (array $attributes) {
                return $attributes['stok_total'];
            },
            'stok_disewa' => 0,
            'spesifikasi' => json_encode([
                'merk' => $this->faker->company(),
                'warna' => $this->faker->colorName(),
                'berat' => $this->faker->numberBetween(1, 20) . ' kg',
                'material' => $this->faker->word()
            ]),
            'is_active' => true,
        ];
    }
}