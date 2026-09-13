<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PageSection extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('page_section_image')
            ->useDisk('cloudinary')
            ->singleFile();

        $this->addMediaCollection('page_section_gallery')
            ->useDisk('cloudinary');

        $this->addMediaCollection('page_section_video')
            ->useDisk('cloudinary')
            ->singleFile();
    }

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'description',
        'cta_label',
        'cta_url',
        'cta2_label',
        'cta2_url',
        'stat_1_value',
        'stat_1_label',
        'stat_2_value',
        'stat_2_label',
        'stat_3_value',
        'stat_3_label',
        'stat_4_value',
        'stat_4_label',
    ];

    /**
     * Ordered lists rendered under this section — e.g. the "Why Choose Us"
     * items on the products page.
     */
    public function contentBlocks(): MorphMany
    {
        return $this->morphMany(ContentBlock::class, 'blockable')
            ->orderBy('sort_order');
    }
}