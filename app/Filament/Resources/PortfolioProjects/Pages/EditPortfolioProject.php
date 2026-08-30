<?php

namespace App\Filament\Resources\PortfolioProjects\Pages;

use App\Filament\Resources\PortfolioProjects\PortfolioProjectResource;
use App\Services\GalleryService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortfolioProject extends EditRecord
{
    protected static string $resource = PortfolioProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Persist the gallery-visibility tick boxes onto each image's custom
     * properties. The field is dehydrated(false), so it never reaches the
     * model — this is where it lands.
     */
    protected function afterSave(): void
    {
        $selected = $this->data['gallery_visible_media'] ?? null;

        // Null means the section was never rendered (e.g. create). Do not
        // treat that as "everything was unticked".
        if (! is_array($selected)) {
            return;
        }

        $selected = array_map('intval', $selected);

        $this->record
            ->getMedia('cover_image')
            ->concat($this->record->getMedia('project_images'))
            ->each(function ($media) use ($selected): void {
                $media->setCustomProperty(
                    GalleryService::SHOW_IN_GALLERY,
                    in_array($media->id, $selected, true),
                );
                $media->save();
            });
    }
}
