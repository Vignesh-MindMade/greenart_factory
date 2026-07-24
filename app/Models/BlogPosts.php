<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BlogPosts extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('Blog_post_image')
            ->useDisk('cloudinary');
        $this->addMediaCollection('author_image')
            ->useDisk('cloudinary');
    }

    //
    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'author',
        'blog_date',
        'published_at',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'blog_date' => 'datetime',
    ];

    public function blogs(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
