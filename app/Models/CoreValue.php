<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreValue extends Model
{
    use LogsActivity, SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'sort_order',
        'status',
    ];
}
