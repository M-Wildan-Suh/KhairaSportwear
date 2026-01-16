<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            // Tambah kolom baru untuk warna dan size
            $table->json('warna')->nullable()->comment('Array warna yang tersedia');
            $table->json('size')->nullable()->comment('Array ukuran yang tersedia');
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['warna', 'size']);
        });
    }
};