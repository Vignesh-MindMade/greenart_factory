<?php

namespace App\Filament\Resources\PageSections;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\PageSections\Pages\ListAboutPageSections;
use App\Filament\Resources\PageSections\Pages\EditAboutPageSection;
use App\Models\PageSection;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use BackedEnum;

// Section headings that are plain text only (no images/CTAs/stats) — the
// media-heavy sections (Hero, Facility) each got their own dedicated
// resource instead. Certifications/Core Values/Team also have their own
// item-list resources; this only edits each block's intro heading text.
class AboutSectionResource extends Resource
{
    use HasModuleAccess;

    public const MODULE = 'about_us';

    private const SECTION_KEYS = [
        'about_story',
        'about_mission',
        'about_vision',
        'about_core_values',
        'about_team',
        'about_certifications',
        'about_clients',
    ];

    protected static ?string $model = PageSection::class;
    protected static string|UnitEnum|null $navigationGroup = 'About';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Section Headings';
    protected static ?string $recordTitleAttribute = 'title';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('section_key', self::SECTION_KEYS);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('section_key')
                ->label('Section')
                ->disabled()
                ->dehydrated(false),
            TextInput::make('title'),
            TextInput::make('subtitle'),
            Textarea::make('description')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('section_key')->label('Section')->badge(),
                TextColumn::make('title')->limit(30)->searchable(),
                TextColumn::make('subtitle')->limit(30)->toggleable()->searchable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => ListAboutPageSections::route('/'),
            'edit'  => EditAboutPageSection::route('/{record}/edit'),
        ];
    }
}