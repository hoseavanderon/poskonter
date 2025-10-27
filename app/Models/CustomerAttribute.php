<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'outlet_id',
        'attribute',
        'attribute_value',
        'attribute_notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
