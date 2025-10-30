<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashbookWallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['cashbook_wallet','outlet_id'];

    public function cashbooks()
    {
        return $this->hasMany(Cashbook::class, 'cashbook_wallet_id');
    }
}
