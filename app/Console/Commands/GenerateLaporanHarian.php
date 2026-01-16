<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LaporanService;
use Carbon\Carbon;

class GenerateLaporanHarian extends Command
{
    protected $signature = 'laporan:harian {date?}';
    protected $description = 'Generate laporan harian otomatis';
    
    protected $laporanService;
    
    public function __construct(LaporanService $laporanService)
    {
        parent::__construct();
        $this->laporanService = $laporanService;
    }
    
    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::yesterday();
        
        $this->info("Generating laporan harian untuk tanggal: " . $date->format('Y-m-d'));
        
        try {
            // Generate untuk penjualan
            $laporanPenjualan = $this->laporanService->generateLaporanPerTipe(
                'penjualan', 
                'harian', 
                $date->copy()->startOfDay(), 
                $date->copy()->endOfDay()
            );
            
            // Generate untuk penyewaan
            $laporanPenyewaan = $this->laporanService->generateLaporanPerTipe(
                'penyewaan', 
                'harian', 
                $date->copy()->startOfDay(), 
                $date->copy()->endOfDay()
            );
            
            // Generate untuk keuangan
            $laporanKeuangan = $this->laporanService->generateLaporanPerTipe(
                'keuangan', 
                'harian', 
                $date->copy()->startOfDay(), 
                $date->copy()->endOfDay()
            );
            
            $this->info("Laporan berhasil digenerate:");
            $this->info("- Laporan Penjualan: {$laporanPenjualan->kode_laporan}");
            $this->info("- Laporan Penyewaan: {$laporanPenyewaan->kode_laporan}");
            $this->info("- Laporan Keuangan: {$laporanKeuangan->kode_laporan}");
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}