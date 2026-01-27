<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

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


    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }


    /* ================= RELATIONS ================= */
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

    public function bundle()
    {
        return $this->belongsTo(ProdukVarian::class, 'bundle_id', 'id');
    }

    /* ================= KODE TRANSAKSI ================= */
    public static function generateKodeTransaksi()
    {
        $date = now()->format('Ymd');

        $last = self::where('kode_transaksi', 'like', 'TRX-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $last
            ? str_pad(((int) substr($last->kode_transaksi, -4)) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        return 'TRX-' . $date . '-' . $number;
    }

    /* ================= AUTO SET KODE ================= */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaksi) {
            if (empty($transaksi->kode_transaksi)) {
                $transaksi->kode_transaksi = self::generateKodeTransaksi();
            }
        });
    }

    /* ================= ACCESSOR ================= */
    public function getBuktiPembayaranUrlAttribute()
    {
        return $this->bukti_pembayaran
            ? asset('storage/bukti-pembayaran/' . $this->bukti_pembayaran)
            : null;
    }

    // ================= SCOPE =================
    public function scopeSelesai(Builder $query)
    {
        return $query->where('status', 'selesai'); // sesuaikan status yang menandakan transaksi selesai
    }

    public function items()
{
    return $this->detailTransaksis(); // alias untuk detailTransaksis
}
}
