<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AboutStory extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('about_story_images')
            ->useDisk('cloudinary')
            ->singleFile();
    }

    protected $fillable = [
        'tab_label',
        'title',
        'slug',
        'content',
        'sort_order',
        'status',
    ];
}
