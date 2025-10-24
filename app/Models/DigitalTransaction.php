<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'digital_product_id',
        'digital_brand_id',
        'device_id',
        'app_id',
        'customer_id',
        'nominal',
        'harga_jual',
        'subtotal',
        'dibayar',
        'kembalian',
        'total',
        'outlet_id',
        'paid_at',
    ];

    public function product()
    {
        return $this->belongsTo(DigitalProduct::class, 'digital_product_id');
    }

    public function brand()
    {
        return $this->belongsTo(DigitalBrand::class, 'digital_brand_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
