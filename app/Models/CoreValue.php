<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class CoreValue extends Model
{
    use LogsActivity;
    protected $fillable = [
        'title',
        'description',
        'sort_order',
        'status',
    ];
}
