<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserDetail;
use App\Models\UserAddress;
use App\Models\ReferralCoupon;
use App\Models\WalletTransaction;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles; // 1. Import Trait

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // 2. Use Trait

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type', // admin, staff, seller, customer
        'status',    // active, pending, inactive
        'phone',
        'wallet_balance'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationship with UserDetails
    public function details()
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }

    // 2. One-to-Many: Multiple Addresses
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function referralCoupon()
    {
        return $this->hasOne(ReferralCoupon::class);
    }
}
