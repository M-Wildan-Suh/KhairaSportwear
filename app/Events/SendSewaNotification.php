<?php
// app/Listeners/SendSewaNotification.php
namespace App\Listeners;

use App\Events\SewaAktif;
use App\Events\SewaAkanBerakhir;
use App\Events\SewaTerlambat;
use App\Events\PengembalianSelesai;
use App\Notifications\SewaAktifNotification;
use App\Notifications\SewaAkanBerakhirNotification;
use App\Notifications\SewaTerlambatNotification;
use App\Notifications\PengembalianSelesaiNotification;

class SendSewaNotification
{
    public function handleSewaAktif(SewaAktif $event)
    {
        // Notifikasi ke user
        $event->sewa->user->notify(new SewaAktifNotification($event->sewa));
        
        // Notifikasi ke admin (jika perlu)
        // $adminUsers = User::where('role', 'admin')->get();
        // Notification::send($adminUsers, new NewSewaNotification($event->sewa));
    }

    public function handleSewaAkanBerakhir(SewaAkanBerakhir $event)
    {
        $event->sewa->user->notify(
            new SewaAkanBerakhirNotification($event->sewa, $event->daysLeft)
        );
    }

    public function handleSewaTerlambat(SewaTerlambat $event)
    {
        $event->sewa->user->notify(
            new SewaTerlambatNotification($event->sewa, $event->daysLate)
        );
        
        // Notifikasi ke admin
        // $adminUsers = User::where('role', 'admin')->get();
        // Notification::send($adminUsers, new SewaTerlambatAdminNotification($event->sewa));
    }

    public function handlePengembalianSelesai(PengembalianSelesai $event)
    {
        $event->pengembalian->sewa->user->notify(
            new PengembalianSelesaiNotification($event->pengembalian)
        );
    }
}