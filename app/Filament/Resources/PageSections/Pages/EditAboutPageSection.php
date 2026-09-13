<?php

namespace App\Filament\Resources\PageSections\Pages;

use App\Filament\Resources\PageSections\AboutSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAboutPageSection extends EditRecord
{
    protected static string $resource = AboutSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
