<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Product;
use App\Models\PortfolioProject;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class ProductVariant extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('variant_images')
            ->useDisk('cloudinary');
    }
    protected $fillable = ['product_id', 'name','slug'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function portfolioProjects(): BelongsToMany
{
    return $this->belongsToMany(
        PortfolioProject::class,
        'portfolio_project_variant'  // explicit pivot name — P2-B rule
    );
}
}
