<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class ProductVariant extends Model
{
    //
    protected $fillable = ['product_id', 'name','slug'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
