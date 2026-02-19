<?php

namespace App\Models;

use App\Models\FilterValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug'];

    public function filterValues()
    {
        return $this->hasMany(FilterValue::class);
    }
}
