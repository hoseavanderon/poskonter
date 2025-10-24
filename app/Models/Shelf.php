<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'code',
        'outlet_id',
    ];

    // Relasi ke outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Relasi ke products (jika dibutuhkan)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
