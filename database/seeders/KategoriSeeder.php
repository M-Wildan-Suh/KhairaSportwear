<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Sepatu Olahraga',
                'slug' => 'sepatu-olahraga',
                'deskripsi' => 'Berbagai jenis sepatu untuk olahraga',
                'icon' => 'fas fa-shoe-prints',
            ],
            [
                'nama' => 'Pakaian Olahraga',
                'slug' => 'pakaian-olahraga',
                'deskripsi' => 'Pakaian untuk berbagai macam olahraga',
                'icon' => 'fas fa-tshirt',
            ],
            [
                'nama' => 'Alat Fitness',
                'slug' => 'alat-fitness',
                'deskripsi' => 'Alat-alat untuk fitness dan latihan',
                'icon' => 'fas fa-dumbbell',
            ],
            [
                'nama' => 'Bola',
                'slug' => 'bola',
                'deskripsi' => 'Berbagai jenis bola olahraga',
                'icon' => 'fas fa-basketball-ball',
            ],
            [
                'nama' => 'Raket',
                'slug' => 'raket',
                'deskripsi' => 'Raket untuk bulutangkis, tenis, dan squash',
                'icon' => 'fas fa-table-tennis',
            ],
            [
                'nama' => 'Aksesoris',
                'slug' => 'aksesoris',
                'deskripsi' => 'Aksesoris pendukung olahraga',
                'icon' => 'fas fa-headphones',
            ],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}