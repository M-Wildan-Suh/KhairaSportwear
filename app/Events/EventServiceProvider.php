<?php
// app/Providers/EventServiceProvider.php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\SewaAktif;
use App\Events\SewaAkanBerakhir;
use App\Events\SewaTerlambat;
use App\Events\PengembalianDiajukan;
use App\Events\PengembalianSelesai;
use App\Listeners\SendSewaNotification;
use App\Listeners\SendPengembalianNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SewaAktif::class => [
            SendSewaNotification::class . '@handleSewaAktif',
        ],
        SewaAkanBerakhir::class => [
            SendSewaNotification::class . '@handleSewaAkanBerakhir',
        ],
        SewaTerlambat::class => [
            SendSewaNotification::class . '@handleSewaTerlambat',
        ],
        PengembalianDiajukan::class => [
            SendPengembalianNotification::class . '@handlePengembalianDiajukan',
        ],
        PengembalianSelesai::class => [
            SendSewaNotification::class . '@handlePengembalianSelesai',
        ],
    ];
}