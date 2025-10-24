<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashbookCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function cashbooks()
    {
        return $this->hasMany(Cashbook::class);
    }
}
