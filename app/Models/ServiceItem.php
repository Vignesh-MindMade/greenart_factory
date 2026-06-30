<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class ServiceItem extends Model implements HasMedia
{
    //
    use InteractsWithMedia;
    protected $fillable = ['service_id', 'name', 'slug', 'order'];
   public function registerMediaCollections(): void
    {
        $this->addMediaCollection('service_item_images')
            ->useDisk('cloudinary');
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
