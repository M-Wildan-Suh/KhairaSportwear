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

    // Relationships
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

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }

    public function denda()
    {
        return $this->hasOneThrough(Denda::class, Pengembalian::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'terlambat');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // Custom methods
    public function generateKodeSewa()
    {
        $date = now()->format('Ymd');
        $lastSewa = self::where('kode_sewa', 'like', 'SEWA-' . $date . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSewa) {
            $lastNumber = (int) substr($lastSewa->kode_sewa, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'SEWA-' . $date . '-' . $newNumber;
    }

    public function hitungKeterlambatan()
    {
        if (!$this->tanggal_kembali_aktual) {
            $today = Carbon::today();
            $tanggalKembali = Carbon::parse($this->tanggal_kembali_rencana);
            
            if ($today->greaterThan($tanggalKembali)) {
                return $today->diffInDays($tanggalKembali);
            }
            return 0;
        }

        $tanggalKembaliAktual = Carbon::parse($this->tanggal_kembali_aktual);
        $tanggalKembaliRencana = Carbon::parse($this->tanggal_kembali_rencana);

        if ($tanggalKembaliAktual->greaterThan($tanggalKembaliRencana)) {
            return $tanggalKembaliAktual->diffInDays($tanggalKembaliRencana);
        }

        return 0;
    }

    public function hitungDenda()
    {
        $keterlambatan = $this->hitungKeterlambatan();
        if ($keterlambatan > 0) {
            $tarifDenda = Konfigurasi::where('kunci', 'denda_per_hari')->first();
            $tarif = $tarifDenda ? $tarifDenda->nilai : 10000;
            return $keterlambatan * $tarif;
        }
        return 0;
    }

    public function updateStatus()
    {
        $today = Carbon::today();
        $tanggalKembali = Carbon::parse($this->tanggal_kembali_rencana);

        if ($this->status === 'aktif' && $today->greaterThan($tanggalKembali)) {
            $this->status = 'terlambat';
            $this->save();
        }
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aktif' => 'badge bg-success',
            'selesai' => 'badge bg-primary',
            'terlambat' => 'badge bg-danger',
            'dibatalkan' => 'badge bg-secondary'
        ];

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-secondary') . '">' . 
               ucfirst($this->status) . 
               '</span>';
    }

    public function getSisaHariAttribute()
    {
        $today = Carbon::today();
        $tanggalKembali = Carbon::parse($this->tanggal_kembali_rencana);
        
        if ($today->greaterThan($tanggalKembali)) {
            return 0;
        }
        
        return $today->diffInDays($tanggalKembali);
    }

    // Event handler
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sewa) {
            if (empty($sewa->kode_sewa)) {
                $sewa->kode_sewa = $sewa->generateKodeSewa();
            }
        });
    }
}