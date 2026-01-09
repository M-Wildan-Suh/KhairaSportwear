<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_laporan',
        'tipe',
        'periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'data_summary',
        'total_penjualan',
        'total_penyewaan',
        'total_denda',
        'total_pendapatan',
        'total_transaksi',
        'total_produk_terjual',
        'total_produk_disewa',
        'dibuat_oleh'
    ];

    protected $casts = [
        'data_summary' => 'array',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'total_penjualan' => 'decimal:2',
        'total_penyewaan' => 'decimal:2',
        'total_denda' => 'decimal:2',
        'total_pendapatan' => 'decimal:2'
    ];

    // Relationships
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // Custom methods
    public function generateKodeLaporan()
    {
        $date = now()->format('Ymd');
        $tipeCode = strtoupper(substr($this->tipe, 0, 3));
        
        $lastLaporan = self::where('kode_laporan', 'like', 'LAP-' . $tipeCode . '-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastLaporan) {
            $lastNumber = (int) substr($lastLaporan->kode_laporan, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'LAP-' . $tipeCode . '-' . $date . '-' . $newNumber;
    }

    public function getTipeLaporanAttribute()
    {
        $types = [
            'penjualan' => 'Penjualan',
            'penyewaan' => 'Penyewaan',
            'denda' => 'Denda',
            'stok' => 'Stok',
            'keuangan' => 'Keuangan'
        ];

        return $types[$this->tipe] ?? ucfirst($this->tipe);
    }

    public function getPeriodeLaporanAttribute()
    {
        $periods = [
            'harian' => 'Harian',
            'mingguan' => 'Mingguan',
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
            'kustom' => 'Kustom'
        ];

        return $periods[$this->periode] ?? ucfirst($this->periode);
    }

    public function getDataSummaryAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setDataSummaryAttribute($value)
    {
        $this->attributes['data_summary'] = json_encode($value);
    }

    // Event handler
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($laporan) {
            if (empty($laporan->kode_laporan)) {
                $laporan->kode_laporan = $laporan->generateKodeLaporan();
            }
        });
    }
}