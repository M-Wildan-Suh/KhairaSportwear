<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sewa_id')->constrained('sewas')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('tanggal_kembali');
            $table->integer('keterlambatan_hari')->default(0);
            $table->enum('kondisi_alat', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->default('baik');
            $table->text('catatan_kondisi')->nullable();
            $table->decimal('denda_keterlambatan', 15, 2)->default(0);
            $table->decimal('denda_kerusakan', 15, 2)->default(0);
            $table->decimal('total_denda', 15, 2)->default(0);
            $table->enum('status', ['menunggu', 'diproses', 'diverifikasi', 'selesai'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalians');
    }
};