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
        'detail_transaksi_id',
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

        public function detailTransaksi()
    {
        return $this->belongsTo(DetailTransaksi::class);
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
     * Hitung denda berdasarkan tanggal kembali dan kondisi alat
     *
     * @param string $tanggalKembali
     * @param string $kondisiAlat
     * @param float|null $tarifDendaPerHari
     * @return array
     */
    public function hitungDenda($tanggalKembali, $kondisiAlat, $tarifDendaPerHari = null)
    {
        Log::debug('Sewa::hitungDenda dipanggil', [
            'sewa_id' => $this->id,
            'tanggal_kembali' => $tanggalKembali,
            'kondisi_alat' => $kondisiAlat,
            'current_status' => $this->status
        ]);

        // Validasi status
        if ($this->status !== self::STATUS_AKTIF) {
            throw new \Exception('Hanya sewa aktif yang dapat dihitung dendanya. Status saat ini: ' . $this->status);
        }

        // Parse tanggal
        $tanggalKembaliDate = Carbon::parse($tanggalKembali);
        $tanggalMulai = Carbon::parse($this->tanggal_mulai);
        $tanggalKembaliRencana = Carbon::parse($this->tanggal_kembali_rencana);

        // Validasi tanggal kembali
        if ($tanggalKembaliDate->lt($tanggalMulai)) {
            throw new \Exception('Tanggal kembali tidak boleh sebelum tanggal mulai sewa.');
        }

        // Hitung keterlambatan
        $keterlambatan = 0;
        if ($tanggalKembaliDate->gt($tanggalKembaliRencana)) {
            $keterlambatan = $tanggalKembaliDate->diffInDays($tanggalKembaliRencana);
        }

        // Dapatkan tarif denda
        if (is_null($tarifDendaPerHari)) {
            $tarifDendaPerHari = Konfigurasi::getValue('denda_per_hari', 10000);
        }

        // Hitung denda keterlambatan
        $dendaKeterlambatan = $keterlambatan * $tarifDendaPerHari;

        // Hitung denda kerusakan
        $dendaKerusakan = 0;
        $hargaProduk = $this->getHargaProduk();

        if ($kondisiAlat !== 'baik') {
            switch ($kondisiAlat) {
                case 'rusak_ringan':
                    $dendaKerusakan = $hargaProduk * 0.1; // 10%
                    break;
                case 'rusak_berat':
                    $dendaKerusakan = $hargaProduk * 0.5; // 50%
                    break;
                case 'hilang':
                    $dendaKerusakan = $hargaProduk; // 100%
                    break;
            }
        }

        $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

        Log::debug('Hasil perhitungan denda:', [
            'keterlambatan_hari' => $keterlambatan,
            'tarif_denda_per_hari' => $tarifDendaPerHari,
            'denda_keterlambatan' => $dendaKeterlambatan,
            'denda_kerusakan' => $dendaKerusakan,
            'total_denda' => $totalDenda,
            'harga_produk' => $hargaProduk
        ]);

        return [
            'keterlambatan_hari' => $keterlambatan,
            'tarif_denda_per_hari' => $tarifDendaPerHari,
            'denda_keterlambatan' => $dendaKeterlambatan,
            'denda_kerusakan' => $dendaKerusakan,
            'total_denda' => $totalDenda,
            'harga_produk' => $hargaProduk,
            'formatted' => [
                'denda_keterlambatan' => 'Rp ' . number_format($dendaKeterlambatan, 0, ',', '.'),
                'denda_kerusakan' => 'Rp ' . number_format($dendaKerusakan, 0, ',', '.'),
                'total_denda' => 'Rp ' . number_format($totalDenda, 0, ',', '.'),
                'tarif_denda_per_hari' => 'Rp ' . number_format($tarifDendaPerHari, 0, ',', '.'),
                'harga_produk' => 'Rp ' . number_format($hargaProduk, 0, ',', '.')
            ]
        ];
    }

    /**
     * Get harga produk untuk perhitungan denda
     * Cek field alternatif jika harga_beli null
     *
     * @return float
     */
    private function getHargaProduk()
    {
        $produk = $this->produk;
        
        if (!$produk) {
            Log::warning('Produk tidak ditemukan untuk sewa: ' . $this->id);
            return 0;
        }

        // Cek harga_beli
        if (!is_null($produk->harga_beli) && $produk->harga_beli > 0) {
            return (float) $produk->harga_beli;
        }

        Log::debug('harga_beli null, mencari alternatif untuk produk: ' . $produk->id);

        // Cek field alternatif
        if (isset($produk->harga_sewa) && $produk->harga_sewa > 0) {
            // Asumsi: harga beli = 5x harga sewa
            $harga = $produk->harga_sewa * 5;
            Log::debug('Menggunakan harga_sewa * 5: ' . $harga);
            return $harga;
        }

        if (isset($produk->harga) && $produk->harga > 0) {
            Log::debug('Menggunakan harga: ' . $produk->harga);
            return (float) $produk->harga;
        }

        // Default jika tidak ada harga
        Log::warning('Menggunakan harga default untuk produk: ' . $produk->id);
        return 1000000; // Rp 1.000.000
    }

    /**
     * Proses pengembalian alat dengan perhitungan denda
     *
     * @param string $tanggalKembali
     * @param string $kondisiAlat
     * @param string|null $catatanKondisi
     * @return Pengembalian
     */
    public function prosesPengembalian($tanggalKembali, $kondisiAlat, $catatanKondisi = null)
    {
        Log::debug('Sewa::prosesPengembalian dipanggil', [
            'sewa_id' => $this->id,
            'tanggal_kembali' => $tanggalKembali,
            'kondisi_alat' => $kondisiAlat
        ]);

        // Validasi status
        if ($this->status !== self::STATUS_AKTIF) {
            throw new \Exception('Hanya sewa aktif yang dapat dikembalikan. Status saat ini: ' . $this->status);
        }

        // Hitung denda
        $denda = $this->hitungDenda($tanggalKembali, $kondisiAlat);

        // Parse tanggal
        $tanggalKembaliDate = Carbon::parse($tanggalKembali);

        // Mulai transaction
        DB::beginTransaction();

        try {
            // Update status sewa
            $this->update([
                'status' => self::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN,
                'tanggal_kembali_aktual' => $tanggalKembaliDate,
                'denda' => $denda['total_denda']
            ]);

            // Buat record pengembalian
            $pengembalian = Pengembalian::create([
                'sewa_id' => $this->id,
                'tanggal_kembali' => $tanggalKembaliDate,
                'keterlambatan_hari' => $denda['keterlambatan_hari'],
                'kondisi_alat' => $kondisiAlat,
                'catatan_kondisi' => $catatanKondisi,
                'denda_keterlambatan' => $denda['denda_keterlambatan'],
                'denda_kerusakan' => $denda['denda_kerusakan'],
                'total_denda' => $denda['total_denda'],
                'status' => 'menunggu'
            ]);

            // Buat record denda jika ada
            if ($denda['total_denda'] > 0) {
                $dendaRecord = Denda::create([
                    'pengembalian_id' => $pengembalian->id,
                    'user_id' => $this->user_id,
                    'tarif_denda_per_hari' => $denda['tarif_denda_per_hari'],
                    'jumlah_hari_terlambat' => $denda['keterlambatan_hari'],
                    'jumlah_denda' => $denda['total_denda'],
                    'status_pembayaran' => 'belum_dibayar',
                    'tanggal_jatuh_tempo' => Carbon::now()->addDays(7),
                    'keterangan' => 'Denda keterlambatan dan kerusakan alat'
                ]);

                Log::debug('Denda created:', ['denda_id' => $dendaRecord->id]);
            }

            // Kembalikan stok
            if ($this->produk) {
                $this->produk->updateStokSewa($this->getQuantity(), 'masuk');
                Log::debug('Stok dikembalikan untuk produk: ' . $this->produk->id);
            }

            // Commit transaction
            DB::commit();

            Log::debug('Pengembalian berhasil diproses:', [
                'pengembalian_id' => $pengembalian->id,
                'total_denda' => $denda['total_denda']
            ]);

            return $pengembalian;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error proses pengembalian: ' . $e->getMessage());
            throw $e;
        }
    }
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

    public function getStatusBadgeColor()
{
    $colors = [
        'diproses' => 'warning',
        'dibayar' => 'info',
        'aktif' => 'success',
        'selesai' => 'primary',
        'dibatalkan' => 'danger'
    ];
    
    return $colors[$this->status] ?? 'secondary';
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