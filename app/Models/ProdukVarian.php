<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukVarian extends Model
{
    protected $fillable = [
        'produk_id',
        'warna',
        'size',
        'stok',
        'stok_disewa'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
