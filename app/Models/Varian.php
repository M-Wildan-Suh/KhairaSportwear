<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Varian extends Model
{
    use HasFactory;

    protected $table = 'varians';

    protected $fillable = [
        'produk_id',
        'warna',
        'size',
        'stok',
        'harga',       // opsional, kalau beda harga
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi ke Produk
     * Satu varian dimiliki satu produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Relasi ke DetailTransaksi
     * Satu varian bisa muncul di banyak transaksi
     */
    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'varian_id');
    }
}
