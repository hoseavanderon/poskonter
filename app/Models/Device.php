<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notes',
        'outlet_id',
        'icon'
    ];

    // Relasi ke outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Relasi ke apps (many-to-many)
    public function apps()
    {
        return $this->belongsToMany(App::class, 'device_app', 'device_id', 'app_id');
    }
}
