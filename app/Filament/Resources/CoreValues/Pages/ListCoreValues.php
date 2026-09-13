<?php

namespace App\Filament\Resources\CoreValues\Pages;

use App\Filament\Resources\CoreValues\CoreValueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoreValues extends ListRecords
{
    protected static string $resource = CoreValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
