<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalBrand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo'];

    public function digitalProducts()
    {
        return $this->belongsToMany(DigitalProduct::class, 'digital_brand_product', 'digital_brand_id', 'digital_product_id');
    }

    public function transactions()
    {
        return $this->hasMany(DigitalTransaction::class);
    }
}
