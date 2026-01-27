<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'tipe_produk',
        'quantity',
        'harga_satuan',
        'subtotal',
        'opsi_sewa'
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'opsi_sewa' => 'array'
    ];

    // Relationships
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
    
    public function bundle()
    {
        return $this->belongsTo(ProdukVarian::class, 'bundle_id', 'id');
    }

    // Custom methods
    public function getOpsiSewaAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setOpsiSewaAttribute($value)
    {
        $this->attributes['opsi_sewa'] = json_encode($value);
    }

    public function getDurasiSewaAttribute()
    {
        return $this->opsi_sewa['durasi'] ?? null;
    }

    public function getTanggalMulaiSewaAttribute()
    {
        return $this->opsi_sewa['tanggal_mulai'] ?? null;
    }

    public function getTanggalSelesaiSewaAttribute()
    {
        return $this->opsi_sewa['tanggal_selesai'] ?? null;
    }
}