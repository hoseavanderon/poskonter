<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_attribute_id',
        'attribute_value',
        'stok',
        'last_restock_date',
        'last_sale_date',
        'outlet_id',
    ];

    // Relasi ke product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productAttribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    // Relasi ke outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Relasi ke inventory histories
    public function inventoryHistories()
    {
        return $this->hasMany(InventoryHistory::class, 'product_attribute_value_id');
    }
}
