<?php

namespace App\Filament\Resources\CertificationBadges\Pages;

use App\Filament\Resources\CertificationBadges\CertificationBadgeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCertificationBadge extends EditRecord
{
    protected static string $resource = CertificationBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
