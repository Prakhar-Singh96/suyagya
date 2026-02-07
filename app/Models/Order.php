<?php

namespace App\Models;

use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'rzp_order_id', 'rzp_payment_id', 'rzp_signature', 'user_id', 'shipping_address',
        'total_amount', 'mrp_total', 'coupon_discount',
        'gaming_discount', 'prepaid_discount', 'coupon_code', 'payment_method',
        'payment_status', 'status'
    ];

    // 💡 CASTING: Automatically convert JSON to Array
    protected $casts = [
        'shipping_address' => 'array', // Database me text/json hai, yahan array milega
        'total_amount' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
