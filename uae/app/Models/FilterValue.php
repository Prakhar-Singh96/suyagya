<?php

namespace App\Models;

use App\Models\Filter;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FilterValue extends Model
{
    use HasFactory;

    protected $fillable = ['filter_id', 'value'];

    public function filter()
    {
        return $this->belongsTo(Filter::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_filter');
    }
}
