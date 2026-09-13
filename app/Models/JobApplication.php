<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class JobApplication extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'job_posting_id',
        'name',
        'email',
        'phone',
        'cover_note',
        'status',
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Resumes live on the local, non-public disk — applicant CVs are only ever
     * downloaded through an authenticated admin action, never via a public URL
     * (unlike every other media collection, which uses the cloudinary disk).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('resume')
            ->useDisk('local')
            ->singleFile();
    }
}
