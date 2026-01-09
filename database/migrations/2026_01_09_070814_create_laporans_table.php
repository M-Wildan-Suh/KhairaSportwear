<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan')->unique();
            $table->enum('tipe', ['penjualan', 'penyewaan', 'denda', 'stok', 'keuangan']);
            $table->enum('periode', ['harian', 'mingguan', 'bulanan', 'tahunan', 'kustom']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->json('data_summary');
            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->decimal('total_penyewaan', 15, 2)->default(0);
            $table->decimal('total_denda', 15, 2)->default(0);
            $table->decimal('total_pendapatan', 15, 2)->default(0);
            $table->integer('total_transaksi')->default(0);
            $table->integer('total_produk_terjual')->default(0);
            $table->integer('total_produk_disewa')->default(0);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};