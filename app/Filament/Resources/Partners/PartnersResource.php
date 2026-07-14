<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Resources\Partners\Pages\CreatePartners;
use App\Filament\Resources\Partners\Pages\EditPartners;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Resources\Partners\Schemas\PartnersForm;
use App\Filament\Resources\Partners\Tables\PartnersTable;
use App\Models\Partners;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PartnersResource extends Resource
{
        protected static string|UnitEnum|null $navigationGroup = 'Homepage';
      protected static ?int $navigationSort = 3;

    protected static ?string $model = Partners::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PartnersForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnersTable::configure($table);
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
            'index' => ListPartners::route('/'),
            'create' => CreatePartners::route('/create'),
            'edit' => EditPartners::route('/{record}/edit'),
        ];
    }
}
