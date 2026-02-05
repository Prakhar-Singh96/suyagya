<?php

namespace App\Models;

use App\Models\Category;
use App\Models\FilterValue;
use App\Models\SubCategory;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\GemstoneVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [

        'category_id',
        'sub_category_id',

        'name',
        'slug',
        'description',

        'mrp_price',
        'discount',
        'price',
        'quantity',
        'sort_order',

        'main_image',
        'main_image_alt',
        'product_main_image',
        'product_main_image_alt',

        'offer_end_time',
        'is_siddh_enabled',
        'siddh_price',

        'meta_title',
        'meta_description',
        'meta_keywords',

        'og_title',
        'og_description',
        'og_image',

        'sku',
        'weight',
        'is_gemstone',

        'story_title',
        'story_content',
        'faq_content',

        'status',
        'is_featured',    // ✅ New
        'is_best_seller', // ✅ New

        'delivery_days',
        'emi_available',

        'astro_planet',
        'astro_rashi',
        'astro_benefits'

    ];

    protected $casts = [
        'offer_end_time' => 'datetime', // Date object me convert karega
        'is_siddh_enabled' => 'boolean',
        'faq_content' => 'array', // 👈 Ye line add karein

        // 🆕 New Casts
        'emi_available' => 'boolean', // 1 ko true, 0 ko false samjhega
        'delivery_days' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function filterValues()
    {
        return $this->belongsToMany(FilterValue::class, 'product_filter', 'product_id', 'filter_value_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function gemstoneVariants()
    {
        return $this->hasMany(GemstoneVariant::class);
    }

    // ✅ 3. NEW: Additional Categories Relationship
    public function additionalCategories()
    {
        // 'product_additional_categories' table use karega
        return $this->belongsToMany(Category::class, 'product_additional_categories', 'product_id', 'category_id')
            ->withPivot('sub_category_id') // Pivot se sub-category id bhi milegi
            ->withTimestamps();
    }

    // ✅ 4. NEW: Additional SubCategories Relationship (Direct Access ke liye)
    public function additionalSubCategories()
    {
        return $this->belongsToMany(SubCategory::class, 'product_additional_categories', 'product_id', 'sub_category_id')
            ->withTimestamps();
    }

    // ✅ NEW: Reviews Relationship
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
