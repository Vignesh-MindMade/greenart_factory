<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CertificationBadge extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certification_badge_images')
            ->useDisk('cloudinary')
            ->singleFile();
    }

    protected $fillable = [
        'title',
        'subtitle',
        'sort_order',
        'status',
    ];
}
