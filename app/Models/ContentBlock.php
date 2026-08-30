<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A single item in an ordered list attached to a page section, product,
 * service or job posting. See the migration for the groups this covers.
 */
class ContentBlock extends Model implements HasMedia
{
    use InteractsWithMedia;

    /** Groups currently in use. Add here as each page is built. */
    public const GROUP_WHY_CHOOSE_US = 'why_choose_us';

    public const GROUPS = [
        self::GROUP_WHY_CHOOSE_US => 'Why Choose Us',
    ];

    protected $fillable = [
        'blockable_type',
        'blockable_id',
        'group',
        'display_no',
        'title',
        'description',
        'value',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        // Not used by why_choose_us, but benefits and certifications will.
        $this->addMediaCollection('icon')
            ->useDisk('cloudinary')
            ->singleFile();
    }

    public function blockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }
}
