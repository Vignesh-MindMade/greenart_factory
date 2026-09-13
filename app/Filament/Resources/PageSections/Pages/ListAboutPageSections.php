<?php

namespace App\Filament\Resources\PageSections\Pages;

use App\Filament\Resources\PageSections\AboutSectionResource;
use Filament\Resources\Pages\ListRecords;

class ListAboutPageSections extends ListRecords
{
    protected static string $resource = AboutSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
