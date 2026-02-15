<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relasi One-to-Many: Satu category memiliki banyak products
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
