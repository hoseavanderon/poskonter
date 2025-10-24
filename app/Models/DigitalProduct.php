<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'digital_category_id', 'name', 'code', 'type', 'base_price', 'is_fixed', 'app_id'
    ];

    public function category()
    {
        return $this->belongsTo(DigitalCategory::class, 'digital_category_id');
    }

    public function brands()
    {
        return $this->belongsToMany(DigitalBrand::class, 'digital_brand_product');
    }

    public function transactions()
    {
        return $this->hasMany(DigitalTransaction::class);
    }

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function digitalCategory()
    {
        return $this->belongsTo(DigitalCategory::class);
    }

    public function digitalBrands()
    {
        return $this->belongsToMany(DigitalBrand::class, 'digital_brand_product', 'digital_product_id', 'digital_brand_id');
    }
}
