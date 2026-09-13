<?php

namespace App\Filament\Resources\AboutFacility\Pages;

use App\Filament\Resources\AboutFacility\AboutFacilityResource;
use Filament\Resources\Pages\ListRecords;

class ListAboutFacility extends ListRecords
{
    protected static string $resource = AboutFacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
