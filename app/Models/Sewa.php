<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Sewa extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'user_id',
        'produk_id',
        'kode_sewa',
        'durasi',
        'jumlah_hari',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'status',
        'total_harga',
        'denda',
        'catatan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
        'total_harga' => 'decimal:2',
        'denda' => 'decimal:2'
    ];

    /* ================= RELATIONS ================= */
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    /* ================= KODE SEWA ================= */
    public static function generateKodeSewa()
    {
        $date = now()->format('Ymd');

        $last = self::where('kode_sewa', 'like', 'SEWA-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $last
            ? str_pad(((int) substr($last->kode_sewa, -4)) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        return 'SEWA-' . $date . '-' . $number;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sewa) {
            if (empty($sewa->kode_sewa)) {
                $sewa->kode_sewa = self::generateKodeSewa();
            }
        });
    }
}
