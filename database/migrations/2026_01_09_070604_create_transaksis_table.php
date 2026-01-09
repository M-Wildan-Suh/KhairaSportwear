<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipe', ['penjualan', 'penyewaan'])->default('penjualan');
            $table->decimal('total_harga', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2);
            $table->enum('status', ['pending', 'diproses', 'dibayar', 'dikirim', 'selesai', 'dibatalkan'])->default('pending');
            $table->enum('metode_pembayaran', ['transfer_bank', 'tunai', 'qris'])->default('transfer_bank');
            $table->string('bukti_pembayaran')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('atas_nama')->nullable();
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->dateTime('tanggal_pengiriman')->nullable();
            $table->string('alamat_pengiriman')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};