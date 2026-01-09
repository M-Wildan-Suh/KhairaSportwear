<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Generate initials avatar
        $name = strtoupper(substr($this->name, 0, 2));
        $colors = ['#2B6CB0', '#38B2AC', '#ED8936', '#48BB78', '#F56565'];
        $color = $colors[crc32($this->email) % count($colors)];
        
        return "https://ui-avatars.com/api/?name={$name}&background={$color}&color=fff&size=200";
    }
    
    public function getStatistics()
    {
        return [
            'total_transactions' => $this->transaksis()->count(),
            'total_spent' => $this->transaksis()->selesai()->sum('total_bayar'),
            'active_rentals' => $this->sewas()->aktif()->count(),
            'member_since' => $this->created_at->diffForHumans(),
        ];
    }

    // Relationships
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function sewas()
    {
        return $this->hasMany(Sewa::class);
    }

    public function pengembalians()
    {
        return $this->hasMany(Pengembalian::class, 'admin_id');
    }

    public function dendas()
    {
        return $this->hasMany(Denda::class);
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'dibuat_oleh');
    }

    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function getUnreadNotificationsCount()
    {
        return $this->notifikasis()->belumDibaca()->count();
    }

    public function getCartCount()
    {
        return $this->keranjangs()->count();
    }

    public function getTotalSpent()
    {
        return $this->transaksis()->selesai()->sum('total_bayar');
    }

    public function getActiveRentals()
    {
        return $this->sewas()->aktif()->count();
    }
}