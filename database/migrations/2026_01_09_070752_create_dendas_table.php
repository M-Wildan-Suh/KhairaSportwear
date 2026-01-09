<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')->constrained('pengembalians')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('kode_denda')->unique();
            $table->decimal('tarif_denda_per_hari', 15, 2);
            $table->integer('jumlah_hari_terlambat');
            $table->decimal('jumlah_denda', 15, 2);
            $table->enum('status_pembayaran', ['belum_dibayar', 'dibayar_sebagian', 'lunas'])->default('belum_dibayar');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_pembayaran')->nullable();
            $table->string('bukti_pembayaran_denda')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dendas');
    }
};