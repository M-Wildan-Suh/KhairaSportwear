<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengembalian extends Model
{
    use HasFactory;

    protected $fillable = [
        'sewa_id',
        'admin_id',
        'tanggal_kembali',
        'keterlambatan_hari',
        'kondisi_alat',
        'catatan_kondisi',
        'denda_keterlambatan',
        'denda_kerusakan',
        'total_denda',
        'status',
        'catatan_admin'
    ];

    protected $casts = [
        'tanggal_kembali' => 'date',
        'denda_keterlambatan' => 'decimal:2',
        'denda_kerusakan' => 'decimal:2',
        'total_denda' => 'decimal:2'
    ];

    // Relationships
    public function sewa()
    {
        return $this->belongsTo(Sewa::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }

    // Scopes
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDiproses($query)
    {
        return $query->where('status', 'diproses');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // Custom methods
    public function getKondisiAlatBadgeAttribute()
    {
        $badges = [
            'baik' => 'badge bg-success',
            'rusak_ringan' => 'badge bg-warning',
            'rusak_berat' => 'badge bg-danger',
            'hilang' => 'badge bg-dark'
        ];

        $labels = [
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang'
        ];

        return '<span class="' . ($badges[$this->kondisi_alat] ?? 'badge bg-secondary') . '">' . 
               ($labels[$this->kondisi_alat] ?? ucfirst($this->kondisi_alat)) . 
               '</span>';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'menunggu' => 'badge bg-warning',
            'diproses' => 'badge bg-info',
            'diverifikasi' => 'badge bg-primary',
            'selesai' => 'badge bg-success'
        ];

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-secondary') . '">' . 
               ucfirst($this->status) . 
               '</span>';
    }

    public function hitungDendaKerusakan()
    {
        $hargaProduk = $this->sewa->produk->harga_beli ?? 0;
        
        switch ($this->kondisi_alat) {
            case 'rusak_ringan':
                return $hargaProduk * 0.1; // 10% dari harga beli
            case 'rusak_berat':
                return $hargaProduk * 0.5; // 50% dari harga beli
            case 'hilang':
                return $hargaProduk; // 100% dari harga beli
            default:
                return 0;
        }
    }
}