<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\PortfolioCategory;
use App\Models\Location;
use App\Models\ProductVariant;
use App\Models\Sector;
use App\Models\InstallationType;
// use App\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class PortfolioProject extends Model implements HasMedia
{
    use InteractsWithMedia;
    
    //
    protected $fillable = ['category_id', 'location_id', 'title', 'slug', 'status', 'is_featured', 'featured_order'];

    protected $casts = [
        'is_featured' => 'boolean',
        'featured_order' => 'integer',
    ];

       public function registerMediaCollections(): void
    {
        $this->addMediaCollection('project_images')
            ->useDisk('cloudinary');
             $this->addMediaCollection('cover_image')
        ->useDisk('cloudinary')
        ->singleFile(); 
    }

    public function category():BelongsTo 
    {
        return $this->belongsTo(PortfolioCategory::class,'category_id');
    }
    public function location():BelongsTo 
    {
        return $this->belongsTo(Location::class);
    }

    public function productVariants():BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'portfolio_project_variant');
    }

    public function sectors():BelongsToMany
    {
        return $this->belongsToMany(Sector::class, 'portfolio_project_sector');
    }

    public function installationTypes():BelongsToMany
    {
        return $this->belongsToMany(InstallationType::class, 'portfolio_project_installation_type');
    }

    // public function media():MorphMany
    // {
    //     return $this->morphMany(Media::class, 'model');
    // }
}
