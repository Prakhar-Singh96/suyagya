<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GemstoneVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'type', 'ratti_size', 'material', 'ring_size', 'price', 'mrp', 'quantity'
    ];
}
