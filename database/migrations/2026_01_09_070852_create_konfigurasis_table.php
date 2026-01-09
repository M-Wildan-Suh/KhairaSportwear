<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasis', function (Blueprint $table) {
            $table->id();
            $table->string('kunci')->unique();
            $table->text('nilai')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('tipe')->default('text');
            $table->json('opsi')->nullable();
            $table->timestamps();
        });

        // Insert default configurations
        DB::table('konfigurasis')->insert([
            [
                'kunci' => 'denda_per_hari',
                'nilai' => '10000',
                'keterangan' => 'Tarif denda keterlambatan per hari (Rp)',
                'tipe' => 'number',
            ],
            [
                'kunci' => 'max_hari_sewa',
                'nilai' => '30',
                'keterangan' => 'Maksimal hari penyewaan',
                'tipe' => 'number',
            ],
            [
                'kunci' => 'min_hari_sewa',
                'nilai' => '1',
                'keterangan' => 'Minimal hari penyewaan',
                'tipe' => 'number',
            ],
            [
                'kunci' => 'admin_email',
                'nilai' => 'admin@sportwear.com',
                'keterangan' => 'Email admin untuk notifikasi',
                'tipe' => 'email',
            ],
            [
                'kunci' => 'bank_transfer',
                'nilai' => '["BCA", "Mandiri", "BRI", "BNI"]',
                'keterangan' => 'Daftar bank untuk transfer',
                'tipe' => 'json',
            ],
            [
                'kunci' => 'no_rekening_admin',
                'nilai' => '1234567890',
                'keterangan' => 'Nomor rekening admin untuk transfer',
                'tipe' => 'text',
            ],
            [
                'kunci' => 'nama_rekening_admin',
                'nilai' => 'Admin SportWear',
                'keterangan' => 'Nama pemilik rekening admin',
                'tipe' => 'text',
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasis');
    }
};