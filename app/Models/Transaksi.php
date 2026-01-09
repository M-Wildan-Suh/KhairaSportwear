<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'tipe',
        'total_harga',
        'diskon',
        'total_bayar',
        'status',
        'metode_pembayaran',
        'bukti_pembayaran',
        'nama_bank',
        'no_rekening',
        'atas_nama',
        'catatan',
        'tanggal_pembayaran',
        'tanggal_pengiriman',
        'alamat_pengiriman'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'tanggal_pembayaran' => 'datetime',
        'tanggal_pengiriman' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function sewa()
    {
        return $this->hasOne(Sewa::class);
    }

    // Scopes
    public function scopePenjualan($query)
    {
        return $query->where('tipe', 'penjualan');
    }

    public function scopePenyewaan($query)
    {
        return $query->where('tipe', 'penyewaan');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }

    // Custom methods
    public function generateKodeTransaksi()
    {
        $date = now()->format('Ymd');
        $lastTransaksi = self::where('kode_transaksi', 'like', 'TRX-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaksi) {
            $lastNumber = (int) substr($lastTransaksi->kode_transaksi, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'TRX-' . $date . '-' . $newNumber;
    }

    public function getBuktiPembayaranUrlAttribute()
    {
        if ($this->bukti_pembayaran) {
            return asset('storage/bukti-pembayaran/' . $this->bukti_pembayaran);
        }
        return null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge bg-warning',
            'diproses' => 'badge bg-info',
            'dibayar' => 'badge bg-primary',
            'dikirim' => 'badge bg-secondary',
            'selesai' => 'badge bg-success',
            'dibatalkan' => 'badge bg-danger'
        ];

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-secondary') . '">' . 
               ucfirst(str_replace('_', ' ', $this->status)) . 
               '</span>';
    }

    public function getTipeBadgeAttribute()
    {
        if ($this->tipe === 'penjualan') {
            return '<span class="badge bg-success">Penjualan</span>';
        } else {
            return '<span class="badge bg-info">Penyewaan</span>';
        }
    }

    // Event handler
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaksi) {
            if (empty($transaksi->kode_transaksi)) {
                $transaksi->kode_transaksi = $transaksi->generateKodeTransaksi();
            }
        });
    }
}