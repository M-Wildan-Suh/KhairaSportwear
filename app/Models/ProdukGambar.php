<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukGambar extends Model
{
    use HasFactory;

    protected $table = 'produk_gambar';

    protected $fillable = [
        'produk_id',
        'gambar',
        'urutan',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function getGambarUrlAttribute()
    {
        return asset('storage/produk/' . $this->gambar);
    }
}