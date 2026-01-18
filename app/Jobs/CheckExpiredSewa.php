<?php
// app/Jobs/CheckExpiredSewa.php
namespace App\Jobs;

use App\Models\Sewa;
use App\Services\SewaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CheckExpiredSewa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Cek sewa yang belum dibayar > 24 jam
        $expiredSewas = Sewa::where('status', Sewa::STATUS_MENUNGGU_PEMBAYARAN)
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        foreach ($expiredSewas as $sewa) {
            SewaService::batalkanSewa(
                $sewa->id, 
                'Pembayaran tidak dilakukan dalam waktu 24 jam',
                'system'
            );
        }

        // Cek sewa aktif yang akan berakhir (untuk notifikasi)
        $sewaAkanBerakhir = SewaService::getSewaAkanBerakhir(1); // 1 hari lagi
        
        foreach ($sewaAkanBerakhir as $sewa) {
            event(new \App\Events\SewaAkanBerakhir($sewa, 1));
        }

        // Cek sewa terlambat
        $sewaTerlambat = SewaService::getSewaTerlambat();
        
        foreach ($sewaTerlambat as $sewa) {
            $daysLate = Carbon::parse($sewa->tanggal_kembali_rencana)->diffInDays(now());
            event(new \App\Events\SewaTerlambat($sewa, $daysLate));
        }
    }
}