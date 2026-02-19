<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'review',
        'media',
        'display_name',
        'email',
        'status'
    ];

    protected $casts = [
        'media' => 'array', // Automatically cast JSON to array
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}