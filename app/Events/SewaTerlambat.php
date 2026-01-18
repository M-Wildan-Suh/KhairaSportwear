<?php
// app/Events/SewaTerlambat.php
namespace App\Events;

use App\Models\Sewa;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SewaTerlambat
{
    use Dispatchable, SerializesModels;

    public $sewa;
    public $daysLate;

    public function __construct(Sewa $sewa, $daysLate)
    {
        $this->sewa = $sewa;
        $this->daysLate = $daysLate;
    }
}