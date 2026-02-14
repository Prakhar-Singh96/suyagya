<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    // 🚀 ये कॉलम्स डेटाबेस में भरने की अनुमति देते हैं
    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'type',
        'description'
    ];

    /**
     * ट्रांजैक्शन किस यूजर का है
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * अगर ट्रांजैक्शन किसी आर्डर से जुड़ा है (जैसे कैशबैक)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
