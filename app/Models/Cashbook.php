<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashbook_wallet_id',
        'deskripsi',
        'nominal',
        'type',
        'outlet_id',
        'cashbook_category_id',
    ];

    // === RELASI ===
    
    /**
     * Relasi ke outlet (satu transaksi dimiliki oleh satu outlet)
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Relasi ke kategori pembukuan
     */
    public function category()
    {
        return $this->belongsTo(CashbookCategory::class, 'cashbook_category_id');
    }

    /**
     * Relasi ke wallet (dompet)
     */
    public function wallet()
    {
        return $this->belongsTo(CashbookWallet::class, 'cashbook_wallet_id');
    }
}
