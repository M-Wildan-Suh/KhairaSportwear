<?php
// app/Console/Kernel.php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CheckExpiredSewa;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Cek expired sewa setiap jam
        $schedule->job(new CheckExpiredSewa)->hourly();
        
        // Cek notifikasi akan berakhir setiap hari jam 8 pagi
        $schedule->call(function () {
            \App\Jobs\SendReminderNotifications::dispatch();
        })->dailyAt('08:00');
    }
}