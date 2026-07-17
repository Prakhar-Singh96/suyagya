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
        'desktop_video',
        'mobile_video',
        'link',
        'sort_order',
        'status'
    ];
}
