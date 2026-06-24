<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\PortfolioCategory;
use App\Models\Location;
use App\Models\ProductVariant;
use App\Models\Sector;
use App\Models\InstallationType;
use App\Models\Media;

class PortfolioProject extends Model
{
    //
    protected $fillable = ['category_id', 'location_id', 'title', 'slug', 'status'];

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

    public function media():MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }
}
