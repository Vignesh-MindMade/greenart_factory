<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BlogPosts;


class BlogCategory extends Model
{
    use LogsActivity;
    //
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'status',
    ];
    public function blogPosts():HasMany
    {
        return $this->hasMany(BlogPosts::class);
    }
}
