<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(DigitalProduct::class);
    }

    public function adminRules()
    {
        return $this->hasMany(DigitalAdminRule::class);
    }
}
