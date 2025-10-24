<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'owner_id',
    ];

    // 🔗 Relasi ke owner (User)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // 🔗 Semua user (kasir/admin) yang bekerja di outlet ini
    public function users()
    {
        return $this->hasMany(User::class, 'outlet_id');
    }

    // 🔗 Produk-produk outlet
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // 🔗 Transaksi outlet
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // 🔗 Rak barang
    public function shelves()
    {
        return $this->hasMany(Shelf::class);
    }

    // 🔗 Karyawan outlet
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // 🔗 Buku kas outlet
    public function cashbooks()
    {
        return $this->hasMany(Cashbook::class);
    }
}
