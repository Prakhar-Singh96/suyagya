<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'code',             // Coupon Name (SAVE10)
        'type',             // fixed or percent
        'value',            // Amount (100 or 10)
        'min_cart_amount',  // Kam se kam kitne ki shopping ho
        'expires_at',       // Date
        'status'            // Active (1) / Inactive (0)
    ];

    // Dates ko automatic Carbon object me badalne ke liye
    protected $casts = [
        'expires_at' => 'date',
    ];
}
