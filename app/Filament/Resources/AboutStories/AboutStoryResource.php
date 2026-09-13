<?php

namespace App\Filament\Resources\AboutStories;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\AboutStories\Pages\CreateAboutStory;
use App\Filament\Resources\AboutStories\Pages\EditAboutStory;
use App\Filament\Resources\AboutStories\Pages\ListAboutStories;
use App\Filament\Resources\AboutStories\Schemas\AboutStoryForm;
use App\Filament\Resources\AboutStories\Tables\AboutStoriesTable;
use App\Models\AboutStory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AboutStoryResource extends Resource
{
    use HasModuleAccess;

    public const MODULE = 'about_us';

    protected static ?string $model = AboutStory::class;

    protected static string|UnitEnum|null $navigationGroup = 'About';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return AboutStoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AboutStoriesTable::configure($table);
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
            'index' => ListAboutStories::route('/'),
            'create' => CreateAboutStory::route('/create'),
            'edit' => EditAboutStory::route('/{record}/edit'),
        ];
    }
}
