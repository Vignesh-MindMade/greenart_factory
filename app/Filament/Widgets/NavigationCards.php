<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Product;
use App\Models\Location;
use App\Models\Sector;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Models\InstallationType;



class NavigationCards extends Widget
{
    protected string $view = 'filament.widgets.navigation-cards';
  protected int|string|array $columnSpan = 'full';

    public function getModules(): array
    {
        return [
            [
                'title' => 'Products',
                'description' => 'Manage products',
                'count' => Product::count(),
                'icon' => 'heroicon-o-cube',
                'url' => route('filament.admin.resources.products.index'),
            ],

            [
                'title' => 'Locations',
                'description' => 'Manage locations',
                'count' => Location::count(),
                'icon' => 'heroicon-o-map-pin',
                'url' => route('filament.admin.resources.locations.index'),
            ],

            [
                'title' => 'Sectors',
                'description' => 'Manage sectors',
                'count' => Sector::count(),
                'icon' => 'heroicon-o-building-office',
                'url' => route('filament.admin.resources.sectors.index'),
            ],

            [
                'title' => 'Portfolio Categories',
                'description' => 'Manage categories',
                'count' => PortfolioCategory::count(),
                'icon' => 'heroicon-o-folder',
                'url' => route('filament.admin.resources.portfolio-categories.index'),
            ],

            [
                'title' => 'Portfolio Projects',
                'description' => 'Manage projects',
                'count' => PortfolioProject::count(),
                'icon' => 'heroicon-o-photo',
                'url' => route('filament.admin.resources.portfolio-projects.index'),
            ],

            [
                'title' => 'Installation Types',
                'description' => 'Manage installation types',
                'count' => InstallationType::count(),
                'icon' => 'heroicon-o-wrench-screwdriver',
                'url' => route('filament.admin.resources.installation-types.index'),
            ],
        ];
    }
}