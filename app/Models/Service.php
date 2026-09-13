<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceItem;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Service extends Model 
{
    use LogsActivity;
    //
  
    protected $fillable = ['name', 'slug', 'status'];

 public function serviceitem(): HasMany
    {
        return $this->hasMany(ServiceItem::class)
         ->orderBy('sort_order');
        // Laravel needs to know: which Model am I connecting to?
    }
  

}
