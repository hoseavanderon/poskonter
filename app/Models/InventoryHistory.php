<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_attribute_value_id',
        'type',
        'pcs',
        'keterangan',
        'outlet_id',
    ];

    // Relasi ke product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttributeValue()
    {
        return $this->belongsTo(\App\Models\ProductAttributeValue::class, 'product_attribute_value_id');
    }

    // Relasi ke outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
