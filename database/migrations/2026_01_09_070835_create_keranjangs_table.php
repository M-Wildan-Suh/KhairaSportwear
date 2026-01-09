<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->enum('tipe', ['jual', 'sewa'])->default('jual');
            $table->integer('quantity')->default(1);
            $table->json('opsi_sewa')->nullable()->comment('JSON: durasi, tanggal mulai');
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
            
            $table->unique(['user_id', 'produk_id', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjangs');
    }
};