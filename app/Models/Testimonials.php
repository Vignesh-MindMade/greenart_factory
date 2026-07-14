<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Testimonials extends Model implements HasMedia
{
        use InteractsWithMedia;
         public function registerMediaCollections(): void
    {
        $this->addMediaCollection('testimonial_image')
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
