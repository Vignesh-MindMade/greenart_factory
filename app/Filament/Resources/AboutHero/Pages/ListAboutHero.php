<?php

namespace App\Filament\Resources\AboutHero\Pages;

use App\Filament\Resources\AboutHero\AboutHeroResource;
use Filament\Resources\Pages\ListRecords;

class ListAboutHero extends ListRecords
{
    protected static string $resource = AboutHeroResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
