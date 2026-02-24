<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'main_image',
        'img_alt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'faqs',
        'status'
    ];

    protected $casts = [
        'faqs' => 'array',
    ];
}
