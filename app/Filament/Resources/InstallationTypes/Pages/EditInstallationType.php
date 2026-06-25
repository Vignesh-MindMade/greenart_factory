<?php

namespace App\Filament\Resources\InstallationTypes\Pages;

use App\Filament\Resources\InstallationTypes\InstallationTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInstallationType extends EditRecord
{
    protected static string $resource = InstallationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
