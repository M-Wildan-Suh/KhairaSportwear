<?php

namespace App\Services;

use App\Models\Sewa;
use App\Models\Pengembalian;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianService
{
    /**
     * Ajukan pengembalian
     */
    public static function ajukanPengembalian($sewaId, $tanggalKembali, $data = [])
    {
        return DB::transaction(function () use ($sewaId, $tanggalKembali, $data) {
            $sewa = Sewa::with(['produk'])->findOrFail($sewaId);

            // Validasi
            if ($sewa->status !== Sewa::STATUS_AKTIF) {
                throw new \Exception('Hanya sewa aktif yang dapat dikembalikan.');
            }

            $tanggalKembali = Carbon::parse($tanggalKembali);
            if ($tanggalKembali->lt($sewa->tanggal_mulai)) {
                throw new \Exception('Tanggal kembali tidak valid.');
            }

            // Hitung keterlambatan
            $terlambatHari = 0;
            $dendaKeterlambatan = 0;
            
            if ($tanggalKembali->gt($sewa->tanggal_kembali_rencana)) {
                $terlambatHari = $tanggalKembali->diffInDays($sewa->tanggal_kembali_rencana);
                $dendaKeterlambatan = self::hitungDendaKeterlambatan($sewa, $terlambatHari);
            }

            // Buat pengembalian
            $pengembalian = Pengembalian::create([
                'sewa_id' => $sewa->id,
                'tanggal_kembali' => $tanggalKembali,
                'keterlambatan_hari' => $terlambatHari,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'status' => 'menunggu',
                'kondisi_alat' => $data['kondisi_alat'] ?? 'baik',
                'catatan_kondisi' => $data['catatan_kondisi'] ?? null
            ]);

            // Update sewa
            $sewa->update([
                'tanggal_kembali_aktual' => $tanggalKembali,
                'status' => Sewa::STATUS_MENUNGGU_VERIFIKASI_PENGEMBALIAN
            ]);

            // Event
            event(new \App\Events\PengembalianDiajukan($pengembalian));

            return $pengembalian;
        });
    }

    /**
     * Verifikasi pengembalian oleh admin
     */
    public static function verifikasiPengembalian($pengembalianId, $adminId, $data = [])
    {
        return DB::transaction(function () use ($pengembalianId, $adminId, $data) {
            $pengembalian = Pengembalian::with(['sewa.produk'])->findOrFail($pengembalianId);

            // Hitung denda kerusakan
            $dendaKerusakan = $pengembalian->hitungDendaKerusakan();
            
            // Jika admin override denda
            if (isset($data['denda_kerusakan'])) {
                $dendaKerusakan = $data['denda_kerusakan'];
            }

            // Total denda
            $totalDenda = $pengembalian->denda_keterlambatan + $dendaKerusakan;

            // Update pengembalian
            $pengembalian->update([
                'admin_id' => $adminId,
                'denda_kerusakan' => $dendaKerusakan,
                'total_denda' => $totalDenda,
                'status' => 'selesai',
                'catatan_admin' => $data['catatan_admin'] ?? null
            ]);

            // Update sewa
            $pengembalian->sewa->update([
                'status' => Sewa::STATUS_SELESAI,
                'denda' => $totalDenda
            ]);

            // Kembalikan stok
            if ($pengembalian->sewa->produk) {
                $quantity = $pengembalian->sewa->getQuantity();
                $produk = $pengembalian->sewa->produk;
                $produk->stok_disewa -= $quantity;
                $produk->stok_tersedia += $quantity;
                $produk->save();
            }

            // Event
            event(new \App\Events\PengembalianSelesai($pengembalian));

            return $pengembalian;
        });
    }

    /**
     * Hitung denda keterlambatan
     */
    private static function hitungDendaKeterlambatan(Sewa $sewa, $terlambatHari)
    {
        if ($terlambatHari <= 0) return 0;

        // Denda 10% dari total harga per hari
        $dendaPerHari = $sewa->total_harga * 0.1;
        return $terlambatHari * $dendaPerHari;
    }

    /**
     * Get pending pengembalian
     */
    public static function getPendingPengembalian()
    {
        return Pengembalian::with(['sewa.user', 'sewa.produk'])
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}