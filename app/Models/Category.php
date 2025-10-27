<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_category',
        'name',
    ];

    // Relasi ke SubCategory
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    // Relasi ke Product
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
