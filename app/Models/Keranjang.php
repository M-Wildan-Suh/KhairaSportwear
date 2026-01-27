<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keranjang extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'produk_id',
        'tipe',
        'quantity',
        'opsi_sewa',
        'harga',
        'subtotal'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'opsi_sewa' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function bundle()
    {
        return $this->belongsTo(ProdukVarian::class, 'bundle_id', 'id');
    }

    // Scopes
    public function scopeTipeJual($query)
    {
        return $query->where('tipe', 'jual');
    }

    public function scopeTipeSewa($query)
    {
        return $query->where('tipe', 'sewa');
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

    public function updateSubtotal()
    {
        if ($this->tipe === 'sewa') {
            $durasi = $this->opsi_sewa['durasi'] ?? 'harian';
            $jumlahHari = $this->opsi_sewa['jumlah_hari'] ?? 1;
            $this->harga = $this->opsi_sewa['durasi'] == 'harian' ? $this->produk->harga_sewa_harian : ($this->opsi_sewa['durasi'] == 'mingguan' ? $this->produk->harga_sewa_mingguan : $this->produk->harga_sewa_bulanan);
            $this->subtotal = $this->harga * $this->quantity;
        } else {
            $this->harga = $this->produk->harga_beli;
            $this->subtotal = $this->harga * $this->quantity;
        }
        
        $this->save();
    }

    public function getTotalHargaSewaAttribute()
    {
        if ($this->tipe === 'sewa') {
            $durasi = $this->opsi_sewa['durasi'] ?? 'harian';
            $jumlahHari = $this->opsi_sewa['jumlah_hari'] ?? 1;
            $hargaPerHari = $this->produk->getHargaSewa($durasi);
            return $hargaPerHari * $jumlahHari;
        }
        return 0;
    }
}