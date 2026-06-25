<?php

namespace App\Filament\Resources\InstallationTypes\Pages;

use App\Filament\Resources\InstallationTypes\InstallationTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInstallationTypes extends ListRecords
{
    protected static string $resource = InstallationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
