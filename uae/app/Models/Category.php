<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',

        'banner_image',
        'icon_image',
        'cover_image',

        'banner_alt',
        'icon_alt',
        'cover_alt',

        'meta_title',
        'meta_description',
        'meta_keywords',

        'story_title',
        'story_content',

        'og_title',
        'og_description',
        'og_image',
        'canonical_url',

        'status'
    ];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

