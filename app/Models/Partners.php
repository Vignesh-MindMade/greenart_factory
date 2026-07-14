<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Partners extends Model implements HasMedia
{
    use InteractsWithMedia;

    //
    protected $fillable = [
        'name',
        'website_url',
        'sort_order',
        'status',
    ];
}
