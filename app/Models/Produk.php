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
        'warna',       // Tambah field warna
        'size',        // Tambah field size
        'spesifikasi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'spesifikasi' => 'array',
        'warna' => 'array',        // Tambah cast untuk warna
        'size' => 'array',         // Tambah cast untuk size
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

    // Tambahkan di bagian relationships (sekitar baris 50-an)
    public function gambarTambahan()
    {
        return $this->hasMany(ProdukGambar::class, 'produk_id')
                    ->orderBy('urutan');
    }

    public function gambarPrimary()
    {
        return $this->hasOne(ProdukGambar::class, 'produk_id')
                    ->where('is_primary', true);
    }

    // Update method getGambarUrlAttribute yang sudah ada
    public function getGambarUrlAttribute()
    {
        // Prioritas 1: Gambar dari kolom gambar (legacy)
        if ($this->gambar) {
            if (filter_var($this->gambar, FILTER_VALIDATE_URL)) {
                return $this->gambar;
            }

            $path = 'produk/' . ltrim($this->gambar, '/');

            if (file_exists(public_path('storage/' . $path))) {
                return asset('storage/' . $path);
            }
        }

        // Prioritas 2: Gambar primary dari tabel produk_gambar
        $primaryImage = $this->gambarPrimary;
        if ($primaryImage) {
            return $primaryImage->gambar_url;
        }

        // Prioritas 3: Gambar pertama dari tabel produk_gambar
        $firstImage = $this->gambarTambahan()->first();
        if ($firstImage) {
            return $firstImage->gambar_url;
        }

        // Fallback: default image
        return 'https://placehold.co/400x400/e5e7eb/6b7280?text=Produk&font=roboto';
    }

    public function getDefaultImage()
    {
        return 'https://placehold.co/400x400/e5e7eb/6b7280?text=Produk&font=roboto';
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
        \Log::info('=== UPDATE STOK SEWA ===');
        \Log::info('Product: ' . $this->nama . ' (ID: ' . $this->id . ')');
        \Log::info('Action: ' . $tipe);
        \Log::info('Quantity: ' . $quantity);
        \Log::info('Before - stok_disewa: ' . $this->stok_disewa . ', stok_tersedia: ' . $this->stok_tersedia);

        if ($tipe === 'keluar') {
            // Produk DISEWA: stok_disewa berkurang, stok_tersedia berkurang
            if ($this->stok_tersedia < $quantity) {
                throw new \Exception("Stok sewa tidak mencukupi. Stok tersedia: {$this->stok_disewa}, dibutuhkan: {$quantity}");
            }

            $this->stok_disewa -= $quantity;
            $this->stok_tersedia -= $quantity;
        } else {
            // Produk DIKEMBALIKAN: stok_disewa bertambah, stok_tersedia bertambah
            $this->stok_disewa += $quantity;
            $this->stok_tersedia += $quantity;
        }

        $this->save();
        $this->refresh();

        \Log::info('After - stok_disewa: ' . $this->stok_disewa . ', stok_tersedia: ' . $this->stok_tersedia);

        return $this;
    }

    // Add these methods to your Produk model:

    // Accessor untuk stok_sewa (alias dari stok_disewa)
    public function getStokSewaAttribute()
    {
        return $this->stok_disewa;
    }

    // Mutator untuk stok_sewa
    public function setStokSewaAttribute($value)
    {
        $this->attributes['stok_disewa'] = $value;
    }

    // Accessor untuk cek ketersediaan sewa
    public function getTersediaSewaAttribute()
    {
        return $this->stok_disewa > 0 && ($this->tipe === 'sewa' || $this->tipe === 'both');
    }

    // Method untuk cek ketersediaan stok sewa
    public function cekStokSewa($quantity)
    {
        return $this->stok_disewa >= $quantity;
    }

    // Accessor untuk mendapatkan array warna
    public function getWarnaListAttribute()
    {
        return $this->warna ?: [];
    }

    // Accessor untuk mendapatkan array size
    public function getSizeListAttribute()
    {
        return $this->size ?: [];
    }

    // Method untuk format warna sebagai string
    public function getWarnaStringAttribute()
    {
        if (!$this->warna || empty($this->warna)) {
            return '-';
        }

        return implode(', ', $this->warna);
    }

    // Method untuk format size sebagai string
    public function getSizeStringAttribute()
    {
        if (!$this->size || empty($this->size)) {
            return '-';
        }

        return implode(', ', $this->size);
    }

    // Method untuk mengecek apakah produk memiliki warna tertentu
    public function hasWarna($warna)
    {
        return $this->warna && in_array($warna, $this->warna);
    }

    // Method untuk mengecek apakah produk memiliki size tertentu
    public function hasSize($size)
    {
        return $this->size && in_array($size, $this->size);
    }

    // Method untuk menambahkan warna
    public function addWarna($warna)
    {
        $warnaList = $this->warna ?: [];

        if (!in_array($warna, $warnaList)) {
            $warnaList[] = $warna;
            $this->warna = $warnaList;
            $this->save();
        }

        return $this;
    }

    // Method untuk menambahkan size
    public function addSize($size)
    {
        $sizeList = $this->size ?: [];

        if (!in_array($size, $sizeList)) {
            $sizeList[] = $size;
            $this->size = $sizeList;
            $this->save();
        }

        return $this;
    }

    // Method varian produk
    public function varians()
    {
        return $this->hasMany(ProdukVarian::class);
    }


    // Accessor untuk harga format
    public function getHargaBeliFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_beli, 0, ',', '.');
    }

    public function getHargaSewaHarianFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_sewa_harian, 0, ',', '.');
    }

    public function getHargaSewa()
    {
        return $this->harga_sewa_harian;
    }

    // Method untuk validasi stok consistency
    public function validateStokConsistency()
    {
        // Untuk produk both, pastikan stok_total >= stok_tersedia + stok_disewa
        if ($this->tipe === 'both') {
            $totalUsed = $this->stok_tersedia + $this->stok_disewa;
            if ($totalUsed > $this->stok_total) {
                throw new \Exception("Stok tidak konsisten. Total stok ({$this->stok_total}) lebih kecil dari stok tersedia ({$this->stok_tersedia}) + stok disewa ({$this->stok_disewa}) = {$totalUsed}");
            }
        }

        // Untuk produk jual, stok_disewa harus 0
        if ($this->tipe === 'jual' && $this->stok_disewa > 0) {
            throw new \Exception("Produk jual tidak boleh memiliki stok disewa");
        }

        // Untuk produk sewa, stok_tersedia bisa berapa saja (untuk display)
        return true;
    }

    // app/Models/Produk.php
    public static function getColorCode($colorName)
    {
        $colorMap = [
            'merah' => '#ef4444',
            'biru' => '#3b82f6',
            'hijau' => '#10b981',
            'kuning' => '#f59e0b',
            'hitam' => '#000000',
            'putih' => '#ffffff',
            'abu-abu' => '#6b7280',
            'coklat' => '#92400e',
            'ungu' => '#8b5cf6',
            'pink' => '#ec4899',
            'orange' => '#f97316',
            'emas' => '#fbbf24',
            'perak' => '#9ca3af'
        ];

        $lowerColor = strtolower($colorName);
        return $colorMap[$lowerColor] ?? '#6b7280';
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

        static::saving(function ($produk) {
            // Validasi stok saat menyimpan
            $produk->validateStokConsistency();
        });
    }
}
