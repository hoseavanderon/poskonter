<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class App extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo'
    ];
    public function devices()
    {
        return $this->belongsToMany(Device::class, 'device_app');
    }
}
