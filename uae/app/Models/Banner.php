<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'desktop_image',
        'mobile_image',
        'link',
        'sort_order',
        'status'
    ];
}
