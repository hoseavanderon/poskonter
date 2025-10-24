<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relasi ke Product
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Relasi ke DigitalBrand (opsional jika terkait digital_products)
    public function digitalBrands()
    {
        return $this->hasMany(DigitalBrand::class);
    }
}