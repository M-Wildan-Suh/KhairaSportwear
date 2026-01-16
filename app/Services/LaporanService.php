<?php

namespace App\Services;

use App\Models\Laporan;
use App\Models\Transaksi;
use App\Models\Sewa;
use Carbon\Carbon;

class LaporanService
{
    /**
     * Catat transaksi ke laporan otomatis saat status selesai
     */
    public function catatTransaksiSelesai(Transaksi $transaksi)
    {
            \Log::info('LaporanService dipanggil untuk transaksi:', [
        'id' => $transaksi->id,
        'kode' => $transaksi->kode_transaksi,
        'status' => $transaksi->status,
        'tipe' => $transaksi->tipe  // Pastikan ini 'penjualan'
    ]);
        // Hanya untuk transaksi dengan status 'selesai'
        if ($transaksi->status !== 'selesai') {
            return;
        }

            // Hanya untuk transaksi dengan status 'selesai'
    if ($transaksi->status !== 'selesai') {
        \Log::warning('Transaksi bukan status selesai:', ['status' => $transaksi->status]);
        return;
    }

    // Hanya untuk tipe penjualan
    if ($transaksi->tipe !== 'penjualan') {
        \Log::warning('Transaksi bukan tipe penjualan:', ['tipe' => $transaksi->tipe]);
        return;
    }

        // Tentukan tipe laporan berdasarkan tipe transaksi
        $tipeLaporan = $transaksi->tipe === 'penyewaan' ? 'penyewaan' : 'penjualan';
        $tanggalTransaksi = $transaksi->completed_at ?? $transaksi->updated_at;
        
        // Cari laporan harian untuk tanggal ini
        $laporan = Laporan::where('tipe', $tipeLaporan)
            ->where('periode', 'harian')
            ->whereDate('tanggal_mulai', $tanggalTransaksi->toDateString())
            ->whereDate('tanggal_selesai', $tanggalTransaksi->toDateString())
            ->first();

        if (!$laporan) {
            // Buat laporan baru untuk hari ini
            $laporan = $this->buatLaporanHarian($tipeLaporan, $tanggalTransaksi);
        }

        // Update data laporan dengan transaksi baru
        $this->updateLaporanDenganTransaksi($laporan, $transaksi);
        
        return $laporan;
    }

    /**
     * Buat laporan harian baru
     */
    private function buatLaporanHarian(string $tipe, Carbon $tanggal)
    {
        $laporan = Laporan::create([
            'tipe' => $tipe,
            'periode' => 'harian',
            'tanggal_mulai' => $tanggal->copy()->startOfDay(),
            'tanggal_selesai' => $tanggal->copy()->endOfDay(),
            'data_summary' => [
                'transaksi_ids' => [],
                'produk_terjual' => [],
                'produk_disewa' => [],
                'kategori_penjualan' => [],
                'kategori_penyewaan' => [],
                'metode_pembayaran' => [],
                'waktu_transaksi' => []
            ],
            'total_penjualan' => 0,
            'total_penyewaan' => 0,
            'total_denda' => 0,
            'total_pendapatan' => 0,
            'total_transaksi' => 0,
            'total_produk_terjual' => 0,
            'total_produk_disewa' => 0,
            'dibuat_oleh' => auth()->id() ?? 1,
        ]);

        return $laporan;
    }

    /**
     * Update laporan dengan transaksi baru
     */
    private function updateLaporanDenganTransaksi(Laporan $laporan, Transaksi $transaksi)
    {
        // Ambil data summary yang ada
        $dataSummary = $laporan->data_summary;
        
        // Tambahkan ID transaksi
        if (!in_array($transaksi->id, $dataSummary['transaksi_ids'])) {
            $dataSummary['transaksi_ids'][] = $transaksi->id;
        }
        
        // Tambahkan waktu transaksi
        $dataSummary['waktu_transaksi'][] = [
            'id' => $transaksi->id,
            'kode_transaksi' => $transaksi->kode_transaksi,
            'waktu' => $transaksi->completed_at ?? $transaksi->updated_at,
            'total' => $transaksi->total_bayar
        ];
        
        // Tambahkan metode pembayaran
        $metodeBayar = $transaksi->metode_pembayaran;
        if (!isset($dataSummary['metode_pembayaran'][$metodeBayar])) {
            $dataSummary['metode_pembayaran'][$metodeBayar] = 0;
        }
        $dataSummary['metode_pembayaran'][$metodeBayar] += $transaksi->total_bayar;
        
        // Hitung statistik berdasarkan tipe transaksi
        if ($transaksi->tipe === 'penjualan') {
            $this->updateLaporanPenjualan($laporan, $transaksi, $dataSummary);
        } elseif ($transaksi->tipe === 'penyewaan') {
            $this->updateLaporanPenyewaan($laporan, $transaksi, $dataSummary);
        }
        
        // Update laporan
        $laporan->update([
            'data_summary' => $dataSummary,
            'total_transaksi' => $laporan->total_transaksi + 1,
            'total_pendapatan' => $laporan->total_pendapatan + $transaksi->total_bayar
        ]);
    }

    /**
     * Update laporan untuk transaksi penjualan
     */
    private function updateLaporanPenjualan(Laporan $laporan, Transaksi $transaksi, array &$dataSummary)
    {
        $totalProdukTerjual = 0;
        
        foreach ($transaksi->detailTransaksis as $detail) {
            $totalProdukTerjual += $detail->quantity;
            
            // Catat produk yang terjual
            $produkId = $detail->produk_id;
            if (!isset($dataSummary['produk_terjual'][$produkId])) {
                $dataSummary['produk_terjual'][$produkId] = [
                    'nama' => $detail->produk->nama ?? 'Produk',
                    'quantity' => 0,
                    'total' => 0
                ];
            }
            $dataSummary['produk_terjual'][$produkId]['quantity'] += $detail->quantity;
            $dataSummary['produk_terjual'][$produkId]['total'] += $detail->subtotal;
            
            // Catat berdasarkan kategori
            if ($detail->produk && $detail->produk->kategori) {
                $kategoriId = $detail->produk->kategori_id;
                if (!isset($dataSummary['kategori_penjualan'][$kategoriId])) {
                    $dataSummary['kategori_penjualan'][$kategoriId] = [
                        'nama' => $detail->produk->kategori->nama ?? 'Kategori',
                        'quantity' => 0,
                        'total' => 0
                    ];
                }
                $dataSummary['kategori_penjualan'][$kategoriId]['quantity'] += $detail->quantity;
                $dataSummary['kategori_penjualan'][$kategoriId]['total'] += $detail->subtotal;
            }
        }
        
        // Update laporan
        $laporan->update([
            'total_penjualan' => $laporan->total_penjualan + $transaksi->total_bayar,
            'total_produk_terjual' => $laporan->total_produk_terjual + $totalProdukTerjual
        ]);
    }

    /**
     * Update laporan untuk transaksi penyewaan
     */
    private function updateLaporanPenyewaan(Laporan $laporan, Transaksi $transaksi, array &$dataSummary)
    {
        $totalProdukDisewa = 0;
        
        foreach ($transaksi->detailTransaksis as $detail) {
            $totalProdukDisewa += $detail->quantity;
            
            // Catat produk yang disewa
            $produkId = $detail->produk_id;
            if (!isset($dataSummary['produk_disewa'][$produkId])) {
                $dataSummary['produk_disewa'][$produkId] = [
                    'nama' => $detail->produk->nama ?? 'Produk',
                    'quantity' => 0,
                    'total' => 0
                ];
            }
            $dataSummary['produk_disewa'][$produkId]['quantity'] += $detail->quantity;
            $dataSummary['produk_disewa'][$produkId]['total'] += $detail->subtotal;
            
            // Catat berdasarkan kategori
            if ($detail->produk && $detail->produk->kategori) {
                $kategoriId = $detail->produk->kategori_id;
                if (!isset($dataSummary['kategori_penyewaan'][$kategoriId])) {
                    $dataSummary['kategori_penyewaan'][$kategoriId] = [
                        'nama' => $detail->produk->kategori->nama ?? 'Kategori',
                        'quantity' => 0,
                        'total' => 0
                    ];
                }
                $dataSummary['kategori_penyewaan'][$kategoriId]['quantity'] += $detail->quantity;
                $dataSummary['kategori_penyewaan'][$kategoriId]['total'] += $detail->subtotal;
            }
        }
        
        // Tambahkan denda jika ada
        $totalDenda = 0;
        if ($transaksi->sewa) {
            $totalDenda = $transaksi->sewa->denda_total ?? 0;
            if (!isset($dataSummary['denda_ids'])) {
                $dataSummary['denda_ids'] = [];
            }
            $dataSummary['denda_ids'][] = $transaksi->sewa->id;
        }
        
        // Update laporan
        $laporan->update([
            'total_penyewaan' => $laporan->total_penyewaan + $transaksi->total_bayar,
            'total_denda' => $laporan->total_denda + $totalDenda,
            'total_produk_disewa' => $laporan->total_produk_disewa + $totalProdukDisewa
        ]);
    }

    /**
     * Generate laporan otomatis untuk periode tertentu
     */
    public function generateLaporanOtomatis(string $periode = 'harian', Carbon $tanggal = null)
    {
        $tanggal = $tanggal ?? now();
        
        switch ($periode) {
            case 'harian':
                $tanggalMulai = $tanggal->copy()->startOfDay();
                $tanggalSelesai = $tanggal->copy()->endOfDay();
                break;
            case 'mingguan':
                $tanggalMulai = $tanggal->copy()->startOfWeek();
                $tanggalSelesai = $tanggal->copy()->endOfWeek();
                break;
            case 'bulanan':
                $tanggalMulai = $tanggal->copy()->startOfMonth();
                $tanggalSelesai = $tanggal->copy()->endOfMonth();
                break;
            case 'tahunan':
                $tanggalMulai = $tanggal->copy()->startOfYear();
                $tanggalSelesai = $tanggal->copy()->endOfYear();
                break;
            default:
                $tanggalMulai = $tanggal->copy()->startOfDay();
                $tanggalSelesai = $tanggal->copy()->endOfDay();
        }
        
        // Generate untuk semua tipe
        $laporanPenjualan = $this->generateLaporanPerTipe('penjualan', $periode, $tanggalMulai, $tanggalSelesai);
        $laporanPenyewaan = $this->generateLaporanPerTipe('penyewaan', $periode, $tanggalMulai, $tanggalSelesai);
        $laporanKeuangan = $this->generateLaporanPerTipe('keuangan', $periode, $tanggalMulai, $tanggalSelesai);
        
        return [
            'penjualan' => $laporanPenjualan,
            'penyewaan' => $laporanPenyewaan,
            'keuangan' => $laporanKeuangan
        ];
    }

    /**
     * Generate laporan untuk tipe tertentu
     */
    private function generateLaporanPerTipe(string $tipe, string $periode, Carbon $tanggalMulai, Carbon $tanggalSelesai)
    {
        // Cek apakah laporan sudah ada
        $existingLaporan = Laporan::where('tipe', $tipe)
            ->where('periode', $periode)
            ->whereDate('tanggal_mulai', $tanggalMulai->toDateString())
            ->whereDate('tanggal_selesai', $tanggalSelesai->toDateString())
            ->first();
            
        if ($existingLaporan) {
            return $existingLaporan;
        }
        
        // Ambil data transaksi untuk periode ini
        $transactions = Transaksi::with(['detailTransaksis.produk.kategori', 'sewa'])
            ->where('status', 'selesai')
            ->whereBetween('completed_at', [$tanggalMulai, $tanggalSelesai])
            ->get();
            
        // Filter berdasarkan tipe jika bukan keuangan
        if ($tipe !== 'keuangan') {
            $transactions = $transactions->where('tipe', $tipe);
        }
        
        // Buat laporan baru
        return $this->buatLaporanDariTransaksi($tipe, $periode, $tanggalMulai, $tanggalSelesai, $transactions);
    }

    /**
     * Buat laporan dari kumpulan transaksi
     */
    private function buatLaporanDariTransaksi(string $tipe, string $periode, Carbon $tanggalMulai, Carbon $tanggalSelesai, $transactions)
    {
        $dataSummary = $this->generateDataSummary($transactions);
        
        $laporan = Laporan::create([
            'tipe' => $tipe,
            'periode' => $periode,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'data_summary' => $dataSummary,
            'total_penjualan' => $transactions->where('tipe', 'penjualan')->sum('total_bayar'),
            'total_penyewaan' => $transactions->where('tipe', 'penyewaan')->sum('total_bayar'),
            'total_denda' => $transactions->where('tipe', 'penyewaan')->sum(function($transaksi) {
                return $transaksi->sewa->denda_total ?? 0;
            }),
            'total_pendapatan' => $transactions->sum('total_bayar'),
            'total_transaksi' => $transactions->count(),
            'total_produk_terjual' => $transactions->where('tipe', 'penjualan')->sum(function($transaksi) {
                return $transaksi->detailTransaksis->sum('quantity');
            }),
            'total_produk_disewa' => $transactions->where('tipe', 'penyewaan')->sum(function($transaksi) {
                return $transaksi->detailTransaksis->sum('quantity');
            }),
            'dibuat_oleh' => auth()->id() ?? 1,
        ]);
        
        return $laporan;
    }

    /**
     * Generate data summary dari transaksi
     */
    private function generateDataSummary($transactions)
    {
        $dataSummary = [
            'transaksi_ids' => [],
            'produk_terjual' => [],
            'produk_disewa' => [],
            'kategori_penjualan' => [],
            'kategori_penyewaan' => [],
            'metode_pembayaran' => [],
            'waktu_transaksi' => []
        ];
        
        foreach ($transactions as $transaksi) {
            $dataSummary['transaksi_ids'][] = $transaksi->id;
            
            $dataSummary['waktu_transaksi'][] = [
                'id' => $transaksi->id,
                'kode_transaksi' => $transaksi->kode_transaksi,
                'waktu' => $transaksi->completed_at ?? $transaksi->updated_at,
                'total' => $transaksi->total_bayar
            ];
            
            // Metode pembayaran
            if (!isset($dataSummary['metode_pembayaran'][$transaksi->metode_pembayaran])) {
                $dataSummary['metode_pembayaran'][$transaksi->metode_pembayaran] = 0;
            }
            $dataSummary['metode_pembayaran'][$transaksi->metode_pembayaran] += $transaksi->total_bayar;
            
            // Produk dan kategori
            foreach ($transaksi->detailTransaksis as $detail) {
                if ($transaksi->tipe === 'penjualan') {
                    $this->addToSummary($dataSummary['produk_terjual'], $detail, 'terjual');
                    if ($detail->produk && $detail->produk->kategori) {
                        $this->addToSummary($dataSummary['kategori_penjualan'], $detail, 'terjual', 'kategori');
                    }
                } elseif ($transaksi->tipe === 'penyewaan') {
                    $this->addToSummary($dataSummary['produk_disewa'], $detail, 'disewa');
                    if ($detail->produk && $detail->produk->kategori) {
                        $this->addToSummary($dataSummary['kategori_penyewaan'], $detail, 'disewa', 'kategori');
                    }
                }
            }
        }
        
        return $dataSummary;
    }

    /**
     * Helper untuk menambah data ke summary
     */
    private function addToSummary(array &$summary, $detail, string $tipe, string $key = 'produk')
    {
        $id = $key === 'produk' ? $detail->produk_id : $detail->produk->kategori_id;
        $nama = $key === 'produk' ? $detail->produk->nama : $detail->produk->kategori->nama;
        
        if (!isset($summary[$id])) {
            $summary[$id] = [
                'nama' => $nama,
                'quantity' => 0,
                'total' => 0
            ];
        }
        
        $summary[$id]['quantity'] += $detail->quantity;
        $summary[$id]['total'] += $detail->subtotal;
    }
}