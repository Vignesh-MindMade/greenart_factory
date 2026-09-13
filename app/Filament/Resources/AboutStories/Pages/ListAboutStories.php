<?php

namespace App\Filament\Resources\AboutStories\Pages;

use App\Filament\Resources\AboutStories\AboutStoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAboutStories extends ListRecords
{
    protected static string $resource = AboutStoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
