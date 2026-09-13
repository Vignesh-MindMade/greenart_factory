<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TeamMember extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('team_member_photos')
            ->useDisk('cloudinary')
            ->singleFile();
    }

    protected $fillable = [
        'name',
        'job_title',
        'company',
        'sort_order',
        'status',
    ];
}
