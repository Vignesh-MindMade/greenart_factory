<?php

namespace App\Filament\Resources\AboutFacility;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\AboutFacility\Pages\EditAboutFacility;
use App\Filament\Resources\AboutFacility\Pages\ListAboutFacility;
use App\Models\PageSection;
use BackedEnum;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AboutFacilityResource extends Resource
{
    use HasModuleAccess;

    public const MODULE = 'about_us';

    protected static ?string $model = PageSection::class;

    protected static string|UnitEnum|null $navigationGroup = 'About';
    protected static ?int $navigationSort = 6;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static ?string $navigationLabel = 'Workshop & Facility';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $slug = 'about-facility';

    // Fixed single row for the about_facility section
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('section_key', 'about_facility');
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            TextInput::make('subtitle'),
            Textarea::make('description')->columnSpanFull(),

            Section::make('Buttons')
                ->columns(2)
                ->schema([
                    TextInput::make('cta_label')->label('Button 1 label'),
                    TextInput::make('cta_url')->label('Button 1 URL')->url(),
                    TextInput::make('cta2_label')->label('Button 2 label'),
                    TextInput::make('cta2_url')->label('Button 2 URL')->url(),
                ]),

            Section::make('Photo Gallery')
                ->description('Workshop / production facility photos.')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('page_section_gallery')
                        ->collection('page_section_gallery')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->maxFiles(10)
                        ->preserveFilenames(),
                ]),

            Section::make('Facility Video')
                ->description('One video file shown for the workshop/facility tour.')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('page_section_video')
                        ->collection('page_section_video')
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                        ->preserveFilenames(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('page_section_gallery')
                    ->collection('page_section_gallery')
                    ->label('Gallery')
                    ->stacked()
                    ->limit(3),
                TextColumn::make('title'),
                TextColumn::make('subtitle'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => ListAboutFacility::route('/'),
            'edit'  => EditAboutFacility::route('/{record}/edit'),
        ];
    }
}
