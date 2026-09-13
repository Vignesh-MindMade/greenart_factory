<?php

namespace App\Filament\Resources\AboutHero;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\AboutHero\Pages\EditAboutHero;
use App\Filament\Resources\AboutHero\Pages\ListAboutHero;
use App\Models\PageSection;
use BackedEnum;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AboutHeroResource extends Resource
{
    use HasModuleAccess;

    public const MODULE = 'about_us';

    protected static ?string $model = PageSection::class;

    protected static string|UnitEnum|null $navigationGroup = 'About';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;
    protected static ?string $navigationLabel = 'Hero';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $slug = 'about-hero';

    // Fixed single row for the about_hero section
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('section_key', 'about_hero');
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->columnSpanFull(),

            Section::make('Buttons')
                ->columns(2)
                ->schema([
                    TextInput::make('cta_label')->label('Button 1 label'),
                    TextInput::make('cta_url')->label('Button 1 URL')->url(),
                    TextInput::make('cta2_label')->label('Button 2 label'),
                    TextInput::make('cta2_url')->label('Button 2 URL')->url(),
                ]),

            Section::make('Statistics')
                ->description('The KPI row under the hero (e.g. "250+ Projects Completed").')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('stat_1_value')->label('Stat 1 value')->placeholder('250+'),
                        TextInput::make('stat_1_label')->label('Stat 1 label')->placeholder('Projects Completed'),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('stat_2_value')->label('Stat 2 value')->placeholder('15+'),
                        TextInput::make('stat_2_label')->label('Stat 2 label')->placeholder('Years of Experience'),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('stat_3_value')->label('Stat 3 value')->placeholder('3+'),
                        TextInput::make('stat_3_label')->label('Stat 3 label')->placeholder('Countries Served'),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('stat_4_value')->label('Stat 4 value')->placeholder('99%'),
                        TextInput::make('stat_4_label')->label('Stat 4 label')->placeholder('Client Satisfaction'),
                    ]),
                ]),

            Section::make('Hero Image')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('page_section_image')
                        ->collection('page_section_image')
                        ->image()
                        ->imagePreviewHeight('200')
                        ->preserveFilenames(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('page_section_image')
                    ->collection('page_section_image')
                    ->label('Image')
                    ->circular(),
                TextColumn::make('title'),
                TextColumn::make('stat_1_value')->label('Stat 1'),
                TextColumn::make('stat_2_value')->label('Stat 2'),
                TextColumn::make('stat_3_value')->label('Stat 3'),
                TextColumn::make('stat_4_value')->label('Stat 4'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => ListAboutHero::route('/'),
            'edit'  => EditAboutHero::route('/{record}/edit'),
        ];
    }
}
