<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TeamMember extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity, SoftDeletes;

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
