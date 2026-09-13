<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Testimonials extends Model implements HasMedia
{
        use InteractsWithMedia, LogsActivity;
         public function registerMediaCollections(): void
    {
        $this->addMediaCollection('testimonial_images')
            ->useDisk('cloudinary')
            ->singleFile();
    }
    //
    protected $fillable = [
        'customer_name',
        'customer_title',
        'quote',
        'sort_order',
        'status',

    ];


}
