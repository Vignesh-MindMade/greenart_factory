<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Certification extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certification_logos')
            ->useDisk('cloudinary')
            ->singleFile();
    }

    protected $fillable = [
        'standard_code',
        'title',
        'description',
        'sort_order',
        'status',
    ];
}
