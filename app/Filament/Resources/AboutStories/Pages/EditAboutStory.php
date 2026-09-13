<?php

namespace App\Filament\Resources\AboutStories\Pages;

use App\Filament\Resources\AboutStories\AboutStoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAboutStory extends EditRecord
{
    protected static string $resource = AboutStoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
