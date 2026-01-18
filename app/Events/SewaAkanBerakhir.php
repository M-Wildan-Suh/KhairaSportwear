<?php
// app/Events/SewaAkanBerakhir.php
namespace App\Events;

use App\Models\Sewa;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SewaAkanBerakhir
{
    use Dispatchable, SerializesModels;

    public $sewa;
    public $daysLeft;

    public function __construct(Sewa $sewa, $daysLeft)
    {
        $this->sewa = $sewa;
        $this->daysLeft = $daysLeft;
    }
}