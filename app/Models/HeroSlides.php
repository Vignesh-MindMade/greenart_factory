<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class HeroSlides extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero_slide_images')
            ->useDisk('cloudinary')
            ->singleFile();
    }

    //
    protected $fillable=[
        'headline',
        'subtext',
        'cta_label',
        'cta_url',
        'sort_order',
        'status'
    ];

}
