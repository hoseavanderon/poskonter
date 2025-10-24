<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'outlet_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 🔗 Relasi ke Outlet tempat user bekerja
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    // 🔗 Jika user adalah pemilik, bisa punya banyak outlet
    public function outlets()
    {
        return $this->hasMany(Outlet::class, 'owner_id');
    }

    // ✅ Helper Role
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'Kasir';
    }
}
