<?php

namespace App\Filament\Resources\InstallationTypes;

use UnitEnum;
use App\Filament\Resources\InstallationTypes\Pages\CreateInstallationType;
use App\Filament\Resources\InstallationTypes\Pages\EditInstallationType;
use App\Filament\Resources\InstallationTypes\Pages\ListInstallationTypes;
use App\Filament\Resources\InstallationTypes\Schemas\InstallationTypeForm;
use App\Filament\Resources\InstallationTypes\Tables\InstallationTypesTable;
use App\Models\InstallationType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InstallationTypeResource extends Resource
{
    protected static ?string $model = InstallationType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Taxonomies';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return InstallationTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstallationTypesTable::configure($table);
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
            'index' => ListInstallationTypes::route('/'),
            'create' => CreateInstallationType::route('/create'),
            'edit' => EditInstallationType::route('/{record}/edit'),
        ];
    }
}
