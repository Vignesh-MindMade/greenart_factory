<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'department',
        'location',
        'employment_type',
        'description',
        'status',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
