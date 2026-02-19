<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_title',
        'story_content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'faq_content'

    ];

    protected $casts = [
        'faq_content' => 'array', // 👈 Cast to array
    ];
}
