<?php

namespace App\Filament\Resources\CoreValues;

use App\Filament\Resources\CoreValues\Pages\CreateCoreValue;
use App\Filament\Resources\CoreValues\Pages\EditCoreValue;
use App\Filament\Resources\CoreValues\Pages\ListCoreValues;
use App\Filament\Resources\CoreValues\Schemas\CoreValueForm;
use App\Filament\Resources\CoreValues\Tables\CoreValuesTable;
use App\Models\CoreValue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CoreValueResource extends Resource
{
    protected static ?string $model = CoreValue::class;

    protected static string|UnitEnum|null $navigationGroup = 'About';
    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CoreValueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoreValuesTable::configure($table);
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
            'index' => ListCoreValues::route('/'),
            'create' => CreateCoreValue::route('/create'),
            'edit' => EditCoreValue::route('/{record}/edit'),
        ];
    }
}
