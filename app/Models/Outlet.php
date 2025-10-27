<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi (mass assignable)
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'owner_id',
    ];

    /**
     * Relasi ke User (pemilik outlet)
     * Setiap outlet dimiliki oleh satu user.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relasi ke Customer
     * Satu outlet memiliki banyak pelanggan.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Relasi ke CustomerAttribute (jika masih digunakan)
     * Satu outlet bisa memiliki banyak atribut pelanggan (optional).
     */
    public function customerAttributes()
    {
        return $this->hasMany(CustomerAttribute::class);
    }
}