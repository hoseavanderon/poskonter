<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'deskripsi',
        'type',
        'nominal',
        'outlet_id',
        'cashbook_category_id',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function category()
    {
        return $this->belongsTo(CashbookCategory::class, 'cashbook_category_id');
    }
}
