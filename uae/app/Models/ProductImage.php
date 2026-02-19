<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image',
        'alt'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
