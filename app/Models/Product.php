<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProductVariant; 

class Product extends Model
{
    //
    protected $fillable = [
       'name', 'slug', 'status'
    ];
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
        // Laravel needs to know: which Model am I connecting to?
    }
}
