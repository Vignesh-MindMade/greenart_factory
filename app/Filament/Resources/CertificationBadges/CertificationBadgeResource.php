<?php

namespace App\Filament\Resources\CertificationBadges;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\CertificationBadges\Pages\CreateCertificationBadge;
use App\Filament\Resources\CertificationBadges\Pages\EditCertificationBadge;
use App\Filament\Resources\CertificationBadges\Pages\ListCertificationBadges;
use App\Filament\Resources\CertificationBadges\Schemas\CertificationBadgeForm;
use App\Filament\Resources\CertificationBadges\Tables\CertificationBadgesTable;
use App\Models\CertificationBadge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CertificationBadgeResource extends Resource
{
    use HasModuleAccess;

    public const MODULE = 'about_us';

    protected static ?string $model = CertificationBadge::class;

    protected static string|UnitEnum|null $navigationGroup = 'About';
    protected static ?int $navigationSort = 8;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CertificationBadgeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificationBadgesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertificationBadges::route('/'),
            'create' => CreateCertificationBadge::route('/create'),
            'edit' => EditCertificationBadge::route('/{record}/edit'),
        ];
    }
}
