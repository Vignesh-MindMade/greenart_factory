<?php

namespace App\Filament\Resources\CertificationBadges\Pages;

use App\Filament\Resources\CertificationBadges\CertificationBadgeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCertificationBadges extends ListRecords
{
    protected static string $resource = CertificationBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
