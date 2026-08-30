<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProductVariant;
use App\Models\ProductVariety;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover_image')
             ->useDisk('cloudinary')
             ->singleFile();

        // Wide banner at the top of this collection's gallery page.
        $this->addMediaCollection('gallery_hero')
             ->useDisk('cloudinary')
             ->singleFile();

        // BRD FR-3.3 — standalone gallery images not tied to any project.
        // Merged with project-sourced images by GalleryService.
        $this->addMediaCollection('product_gallery')
             ->useDisk('cloudinary');
    }
    
    //
    protected $fillable = [
        'name',
        'slug',
        'status',
        'description',
        'varieties_title',
        'varieties_intro',
        'varieties_footer',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
        // Laravel needs to know: which Model am I connecting to?
    }

    public function varieties(): HasMany
    {
        return $this->hasMany(ProductVariety::class)->orderBy('sort_order');
    }
}
