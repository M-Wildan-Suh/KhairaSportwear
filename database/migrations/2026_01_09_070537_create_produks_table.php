<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['jual', 'sewa', 'both'])->default('jual');
            $table->decimal('harga_beli', 15, 2)->nullable()->comment('Harga untuk pembelian');
            $table->decimal('harga_sewa_harian', 15, 2)->nullable()->comment('Harga sewa per hari');
            $table->decimal('harga_sewa_mingguan', 15, 2)->nullable()->comment('Harga sewa per minggu');
            $table->decimal('harga_sewa_bulanan', 15, 2)->nullable()->comment('Harga sewa per bulan');
            $table->integer('stok_total')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->integer('stok_disewa')->default(0);
            $table->json('spesifikasi')->nullable();
            $table->string('gambar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};