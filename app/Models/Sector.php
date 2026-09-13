<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PortfolioProject;

class Sector extends Model
{
    use LogsActivity, SoftDeletes;
    //
    protected $fillable =['name','slug'];

    public function portfolioProjects():BelongsToMany
    {
        return $this->belongsToMany(PortfolioProject::class,'portfolio_project_sector');
    } 
}
