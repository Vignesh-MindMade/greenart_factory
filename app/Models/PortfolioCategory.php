<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PortfolioProject;

class PortfolioCategory extends Model
{
    //
    protected $fillable = ['name', 'slug'];

    public function portfolioProjects(): HasMany
    {
        return $this->hasMany(PortfolioProject::class, 'category_id');
    }
}
