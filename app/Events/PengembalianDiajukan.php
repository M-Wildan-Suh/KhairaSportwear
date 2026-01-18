<?php
// app/Events/PengembalianDiajukan.php
namespace App\Events;

use App\Models\Pengembalian;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PengembalianDiajukan implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pengembalian;

    public function __construct(Pengembalian $pengembalian)
    {
        $this->pengembalian = $pengembalian;
    }

    public function broadcastOn()
    {
        return new Channel('admin');
    }

    public function broadcastAs()
    {
        return 'pengembalian.diajukan';
    }
}