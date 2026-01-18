<?php
// app/Events/SewaAktif.php
namespace App\Events;

use App\Models\Sewa;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SewaAktif implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sewa;

    public function __construct(Sewa $sewa)
    {
        $this->sewa = $sewa;
    }

    public function broadcastOn()
    {
        return new Channel('sewa');
    }

    public function broadcastAs()
    {
        return 'sewa.aktif';
    }
}