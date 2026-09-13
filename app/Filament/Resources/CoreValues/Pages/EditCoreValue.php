<?php

namespace App\Filament\Resources\CoreValues\Pages;

use App\Filament\Resources\CoreValues\CoreValueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoreValue extends EditRecord
{
    protected static string $resource = CoreValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
