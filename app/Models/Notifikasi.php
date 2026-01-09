<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'dibaca',
        'link'
    ];

    protected $casts = [
        'dibaca' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeBelumDibaca($query)
    {
        return $query->where('dibaca', false);
    }

    public function scopeTerbaru($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Custom methods
    public function getTipeIconAttribute()
    {
        $icons = [
            'info' => 'fas fa-info-circle text-info',
            'success' => 'fas fa-check-circle text-success',
            'warning' => 'fas fa-exclamation-triangle text-warning',
            'danger' => 'fas fa-times-circle text-danger',
            'transaksi' => 'fas fa-shopping-cart text-primary',
            'sewa' => 'fas fa-calendar-alt text-info',
            'denda' => 'fas fa-exclamation-circle text-danger'
        ];

        return $icons[$this->tipe] ?? 'fas fa-bell text-secondary';
    }

    public function markAsRead()
    {
        $this->dibaca = true;
        $this->save();
    }

    public static function createNotifikasi($userId, $judul, $pesan, $tipe = 'info', $link = null)
    {
        return self::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'link' => $link
        ]);
    }
}