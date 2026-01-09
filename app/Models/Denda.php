<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Denda extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengembalian_id',
        'user_id',
        'kode_denda',
        'tarif_denda_per_hari',
        'jumlah_hari_terlambat',
        'jumlah_denda',
        'status_pembayaran',
        'tanggal_jatuh_tempo',
        'tanggal_pembayaran',
        'bukti_pembayaran_denda',
        'keterangan'
    ];

    protected $casts = [
        'tarif_denda_per_hari' => 'decimal:2',
        'jumlah_denda' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_pembayaran' => 'date'
    ];

    // Relationships
    public function pengembalian()
    {
        return $this->belongsTo(Pengembalian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sewa()
    {
        return $this->hasOneThrough(Sewa::class, Pengembalian::class);
    }

    // Scopes
    public function scopeBelumDibayar($query)
    {
        return $query->where('status_pembayaran', 'belum_dibayar');
    }

    public function scopeLunas($query)
    {
        return $query->where('status_pembayaran', 'lunas');
    }

    // Custom methods
    public function generateKodeDenda()
    {
        $date = now()->format('Ymd');
        $lastDenda = self::where('kode_denda', 'like', 'DENDA-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastDenda) {
            $lastNumber = (int) substr($lastDenda->kode_denda, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'DENDA-' . $date . '-' . $newNumber;
    }

    public function getStatusPembayaranBadgeAttribute()
    {
        $badges = [
            'belum_dibayar' => 'badge bg-danger',
            'dibayar_sebagian' => 'badge bg-warning',
            'lunas' => 'badge bg-success'
        ];

        $labels = [
            'belum_dibayar' => 'Belum Dibayar',
            'dibayar_sebagian' => 'Dibayar Sebagian',
            'lunas' => 'Lunas'
        ];

        return '<span class="' . ($badges[$this->status_pembayaran] ?? 'badge bg-secondary') . '">' . 
               ($labels[$this->status_pembayaran] ?? ucfirst($this->status_pembayaran)) . 
               '</span>';
    }

    public function getBuktiPembayaranUrlAttribute()
    {
        if ($this->bukti_pembayaran_denda) {
            return asset('storage/bukti-denda/' . $this->bukti_pembayaran_denda);
        }
        return null;
    }

    public function isTerlambatBayar()
    {
        if ($this->status_pembayaran !== 'lunas' && now()->greaterThan($this->tanggal_jatuh_tempo)) {
            return true;
        }
        return false;
    }

    // Event handler
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($denda) {
            if (empty($denda->kode_denda)) {
                $denda->kode_denda = $denda->generateKodeDenda();
            }
        });
    }
}