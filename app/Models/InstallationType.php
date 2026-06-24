<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\PortfolioProject;

class InstallationType extends Model
{
    //
    protected $fillable = ['name', 'slug'];

    public function portfolioProjects():BelongsToMany
    {
        return $this->belongsToMany(PortfolioProject::class,'portfolio_project_installation_type');
    }
}
