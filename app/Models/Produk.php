<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'nama',
        'slug',
        'deskripsi',
        'tipe',
        'harga_beli',
        'harga_sewa_harian',
        'harga_sewa_mingguan',
        'harga_sewa_bulanan',
        'stok_total',
        'stok_tersedia',
        'stok_disewa',
        'spesifikasi',
        'gambar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'spesifikasi' => 'array',
        'harga_beli' => 'decimal:2',
        'harga_sewa_harian' => 'decimal:2',
        'harga_sewa_mingguan' => 'decimal:2',
        'harga_sewa_bulanan' => 'decimal:2'
    ];

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function sewas()
    {
        return $this->hasMany(Sewa::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTipeJual($query)
    {
        return $query->where('tipe', 'jual')->orWhere('tipe', 'both');
    }

    public function scopeTipeSewa($query)
    {
        return $query->where('tipe', 'sewa')->orWhere('tipe', 'both');
    }

    public function scopeStokTersedia($query)
    {
        return $query->where('stok_tersedia', '>', 0);
    }

    // Custom methods
    public function getHargaSewa($durasi)
    {
        switch ($durasi) {
            case 'harian':
                return $this->harga_sewa_harian;
            case 'mingguan':
                return $this->harga_sewa_mingguan;
            case 'bulanan':
                return $this->harga_sewa_bulanan;
            default:
                return $this->harga_sewa_harian;
        }
    }

    public function updateStok($quantity, $tipe = 'keluar')
    {
        if ($tipe === 'keluar') {
            $this->stok_tersedia -= $quantity;
        } else {
            $this->stok_tersedia += $quantity;
        }
        
        $this->save();
    }

    public function updateStokSewa($quantity, $tipe = 'keluar')
    {
        if ($tipe === 'keluar') {
            $this->stok_disewa += $quantity;
            $this->stok_tersedia -= $quantity;
        } else {
            $this->stok_disewa -= $quantity;
            $this->stok_tersedia += $quantity;
        }
        
        $this->save();
    }

    public function getGambarUrlAttribute()
    {
        // Jika tidak ada gambar
        if (!$this->gambar) {
            return $this->getDefaultImage();
        }
        
        // Cek jika gambar adalah URL lengkap
        if (filter_var($this->gambar, FILTER_VALIDATE_URL)) {
            return $this->gambar;
        }
        
        // Untuk gambar di storage (yang paling umum)
        try {
            if (Storage::disk('public')->exists($this->gambar)) {
                return Storage::disk('public')->url($this->gambar);
            }
        } catch (\Exception $e) {
            // Log error jika diperlukan
            \Log::error('Error getting image URL: ' . $e->getMessage());
        }
        
        // Fallback: coba path lain
        return $this->getDefaultImage();
    }

        protected function getDefaultImage()
    {
        // Gunakan placeholder yang pasti bekerja
        return 'https://placehold.co/400x400/e5e7eb/6b7280?text=Produk&font=roboto';
    }

    // Generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($produk) {
            if (empty($produk->slug)) {
                $produk->slug = \Str::slug($produk->nama);
            }
        });
    }
}