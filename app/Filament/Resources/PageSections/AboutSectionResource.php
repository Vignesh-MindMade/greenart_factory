<?php

namespace App\Filament\Resources\PageSections;

use App\Filament\Resources\PageSections\Pages\ListPageSections;
use App\Filament\Resources\PageSections\Pages\CreatePageSection;
use App\Filament\Resources\PageSections\Pages\EditPageSection;
use App\Filament\Resources\PageSections\Schemas\PageSectionForm;
use App\Filament\Resources\PageSections\Tables\PageSectionsTable;
use App\Models\PageSection;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use BackedEnum;

class AboutSectionResource extends Resource
{
    protected static ?string $model = PageSection::class;
    protected static string|UnitEnum|null $navigationGroup = 'About';    // ← ?string not UnitEnum
    protected static ?int $navigationSort = 1;
   protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'About Sections';
    protected static ?string $recordTitleAttribute = 'section_key';

    // Only shows about_* rows — admin never sees homepage sections here
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('section_key', 'like', 'about_%');
    }

    // Reuses the same form and table as Homepage sections — no duplication
    public static function form(Schema $schema): Schema
    {
        return PageSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageSectionsTable::configure($table);
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListPageSections::route('/'),
            'create' => CreatePageSection::route('/create'),
            'edit'   => EditPageSection::route('/{record}/edit'),
        ];
    }
}