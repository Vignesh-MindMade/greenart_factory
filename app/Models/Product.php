<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProductVariant; 
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('products')
             ->useDisk('cloudinary')
             ->singleFile();
    }
    
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
