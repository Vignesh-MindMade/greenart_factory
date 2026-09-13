<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PortfolioProject;

class PortfolioCategory extends Model
{
    use LogsActivity, SoftDeletes;
    //
    protected $fillable = ['name', 'slug'];

    public function portfolioProjects(): HasMany
    {
        return $this->hasMany(PortfolioProject::class, 'category_id');
    }
}
