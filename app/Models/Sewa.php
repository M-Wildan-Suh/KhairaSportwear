<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Sewa extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';
    const STATUS_MENUNGGU_KONFIRMASI = 'menunggu_konfirmasi';
    const STATUS_AKTIF = 'aktif';
    const STATUS_SELESAI = 'selesai';
    const STATUS_DIBATALKAN = 'dibatalkan';
    const STATUS_EXPIRED = 'expired';
    const STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN = 'menunggu_verifikasi_pengembalian';

    // Durasi constants
    const DURASI_HARIAN = 'harian';
    const DURASI_MINGGUAN = 'mingguan';
    const DURASI_BULANAN = 'bulanan';

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
        'catatan',
        'alasan_pembatalan',
        'dibatalkan_oleh',
        'tanggal_pembatalan',
        'verifikasi_oleh',
        'tanggal_verifikasi',
        'status_extend',
        'data_tambahan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
        'tanggal_pembatalan' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'total_harga' => 'decimal:2',
        'denda' => 'decimal:2',
        'data_tambahan' => 'array',
        'jumlah_hari' => 'integer'
    ];

    protected $attributes = [
        'status' => self::STATUS_MENUNGGU_PEMBAYARAN,
        'denda' => 0,
        'jumlah_hari' => 1
    ];

    /* ================= RELATIONS ================= */
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
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

            // Set tanggal_selesai jika belum ada
            if (empty($sewa->tanggal_selesai) && $sewa->tanggal_mulai && $sewa->jumlah_hari) {
                $sewa->tanggal_selesai = Carbon::parse($sewa->tanggal_mulai)
                    ->addDays($sewa->jumlah_hari);
            }

            // Set tanggal_kembali_rencana sama dengan tanggal_selesai
            if (empty($sewa->tanggal_kembali_rencana) && $sewa->tanggal_selesai) {
                $sewa->tanggal_kembali_rencana = $sewa->tanggal_selesai;
            }
        });

        // Auto-expire pending rentals
        static::saving(function ($sewa) {
            if ($sewa->status === self::STATUS_MENUNGGU_PEMBAYARAN) {
                $expiredHours = config('sewa.expired_hours', 24);
                $createdAt = $sewa->transaksi->created_at ?? $sewa->created_at;
                
                if ($createdAt && $createdAt->diffInHours(now()) > $expiredHours) {
                    $sewa->status = self::STATUS_EXPIRED;
                }
            }
        });
    }

    // ================= SCOPES =================
    public function scopeMenungguPembayaran(Builder $query)
    {
        return $query->where('status', self::STATUS_MENUNGGU_PEMBAYARAN);
    }

    public function scopeMenungguKonfirmasi(Builder $query)
    {
        return $query->where('status', self::STATUS_MENUNGGU_KONFIRMASI);
    }

    public function scopeAktif(Builder $query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function scopeSelesai(Builder $query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    public function scopeDibatalkan(Builder $query)
    {
        return $query->where('status', self::STATUS_DIBATALKAN);
    }

    public function scopeExpired(Builder $query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function scopeMenungguVerifikasiPengembalian(Builder $query)
    {
        return $query->where('status', self::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN);
    }

    public function scopeTerlambat(Builder $query)
    {
        return $query->where('status', self::STATUS_AKTIF)
            ->where('tanggal_kembali_rencana', '<', now());
    }

    public function scopeAkanBerakhir(Builder $query, $days = 3)
    {
        return $query->where('status', self::STATUS_AKTIF)
            ->whereBetween('tanggal_kembali_rencana', [now(), now()->addDays($days)]);
    }

    // ================= ACCESSORS =================
    public function getSisaHariAttribute()
    {
        if ($this->status !== self::STATUS_AKTIF) {
            return 0;
        }

        $today = Carbon::today();
        $tanggalKembali = Carbon::parse($this->tanggal_kembali_rencana);

        if ($tanggalKembali->isPast()) {
            return 0;
        }

        return $today->diffInDays($tanggalKembali);
    }

    public function getHariTerlambatAttribute()
    {
        if (!$this->tanggal_kembali_aktual || !$this->tanggal_kembali_rencana) {
            return 0;
        }

        $kembaliAktual = Carbon::parse($this->tanggal_kembali_aktual);
        $kembaliRencana = Carbon::parse($this->tanggal_kembali_rencana);

        if ($kembaliAktual <= $kembaliRencana) {
            return 0;
        }

        return $kembaliAktual->diffInDays($kembaliRencana);
    }

    public function getIsTerlambatAttribute()
    {
        if ($this->status !== self::STATUS_AKTIF) {
            return false;
        }

        return Carbon::parse($this->tanggal_kembali_rencana)->isPast();
    }

    public function getIsAkanBerakhirAttribute()
    {
        if ($this->status !== self::STATUS_AKTIF) {
            return false;
        }

        $daysRemaining = $this->sisa_hari;
        return $daysRemaining > 0 && $daysRemaining <= 3;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_MENUNGGU_PEMBAYARAN => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Pembayaran</span>',
            self::STATUS_MENUNGGU_KONFIRMASI => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Menunggu Konfirmasi</span>',
            self::STATUS_AKTIF => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>',
            self::STATUS_SELESAI => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Selesai</span>',
            self::STATUS_DIBATALKAN => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>',
            self::STATUS_EXPIRED => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Expired</span>',
            self::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Menunggu Verifikasi</span>',
        ];

        return $badges[$this->status] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
    }

    // ================= BUSINESS LOGIC =================
    
    /**
     * Aktifkan sewa setelah pembayaran diverifikasi
     */
    public function aktifkan()
    {
        if ($this->status !== self::STATUS_MENUNGGU_PEMBAYARAN) {
            throw new \Exception('Sewa tidak dapat diaktifkan. Status saat ini: ' . $this->status);
        }

        DB::transaction(function () {
            // Update status sewa
            $this->update([
                'status' => self::STATUS_AKTIF,
                'tanggal_verifikasi' => now()
            ]);

            // Kurangi stok produk
            if ($this->produk) {
                $this->produk->updateStokSewa($this->getQuantity(), 'keluar');
            }
        });

        return $this;
    }

    /**
     * Batalkan sewa
     */
    public function batalkan($alasan = null, $dibatalkanOleh = null)
    {
        if (!in_array($this->status, [self::STATUS_MENUNGGU_PEMBAYARAN, self::STATUS_AKTIF])) {
            throw new \Exception('Sewa tidak dapat dibatalkan. Status saat ini: ' . $this->status);
        }

        DB::transaction(function () use ($alasan, $dibatalkanOleh) {
            // Kembalikan stok jika sewa sudah aktif
            if ($this->status === self::STATUS_AKTIF && $this->produk) {
                $this->produk->updateStokSewa($this->getQuantity(), 'masuk');
            }

            // Update sewa
            $this->update([
                'status' => self::STATUS_DIBATALKAN,
                'alasan_pembatalan' => $alasan,
                'dibatalkan_oleh' => $dibatalkanOleh ?? auth()->id(),
                'tanggal_pembatalan' => now()
            ]);

            // Batalkan transaksi jika ada
            if ($this->transaksi) {
                $this->transaksi->update(['status' => 'dibatalkan']);
            }
        });

        return $this;
    }

    /**
     * Ajukan pengembalian alat
     */
    public function ajukanPengembalian($tanggalKembali, $kondisiAlat, $catatan = null, $foto = null)
    {
        if ($this->status !== self::STATUS_AKTIF) {
            throw new \Exception('Hanya sewa aktif yang dapat dikembalikan.');
        }

        $tanggalKembali = Carbon::parse($tanggalKembali);
        
        if ($tanggalKembali->lt($this->tanggal_mulai)) {
            throw new \Exception('Tanggal kembali tidak boleh sebelum tanggal mulai sewa.');
        }

        // Update status menunggu verifikasi
        $this->update([
            'status' => self::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN,
            'tanggal_kembali_aktual' => $tanggalKembali
        ]);

        return $this;
    }

    /**
     * Verifikasi pengembalian alat (oleh admin)
     */
    public function verifikasiPengembalian($verifikasiOleh = null, $catatan = null)
    {
        if ($this->status !== self::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN) {
            throw new \Exception('Hanya sewa yang menunggu verifikasi yang dapat diverifikasi.');
        }

        DB::transaction(function () use ($verifikasiOleh, $catatan) {
            // Kembalikan stok
            if ($this->produk) {
                $this->produk->updateStokSewa($this->getQuantity(), 'masuk');
            }

            // Update status menjadi selesai
            $this->update([
                'status' => self::STATUS_SELESAI,
                'verifikasi_oleh' => $verifikasiOleh ?? auth()->id(),
                'tanggal_verifikasi' => now(),
                'catatan' => $catatan
            ]);
        });

        return $this;
    }

    /**
     * Get quantity from transaction details
     */
    private function getQuantity()
    {
        if ($this->transaksi && $this->transaksi->detailTransaksis) {
            return $this->transaksi->detailTransaksis
                ->where('produk_id', $this->produk_id)
                ->first()
                ->quantity ?? 1;
        }

        return 1;
    }

    /**
     * Calculate remaining days
     */
    public function hitungSisaHari()
    {
        return $this->sisa_hari;
    }

    /**
     * Calculate late days
     */
    public function hitungKeterlambatan()
    {
        return $this->hari_terlambat;
    }
}