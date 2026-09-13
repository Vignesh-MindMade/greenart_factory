<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreValue extends Model
{
    protected $fillable = [
        'title',
        'description',
        'sort_order',
        'status',
    ];
}
