<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalAdminRule extends Model
{
    use HasFactory;

    protected $fillable = ['digital_category_id','min_nominal','max_nominal','admin_fee'];

    public function category()
    {
        return $this->belongsTo(DigitalCategory::class, 'digital_category_id');
    }
}
