<?php

namespace App\Filament\Resources\PageSections\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PageSectionForm
{
    // Sections that show a single hero/feature image (mission & vision portraits,
    // the certifications frame photo, the hero banner image)
    private const IMAGE_SECTIONS = ['about_hero', 'about_mission', 'about_vision', 'about_certifications'];

    // Sections that show a multi-image gallery (workshop/facility photos)
    private const GALLERY_SECTIONS = ['about_facility'];

    // Only the hero section carries the 4 KPI stat pairs (250+ Projects, etc.)
    private const STATS_SECTIONS = ['about_hero'];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('section_key')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('title'),
                TextInput::make('subtitle'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('cta_label'),
                TextInput::make('cta_url')
                    ->url(),
                TextInput::make('cta2_label'),
                TextInput::make('cta2_url')
                    ->url(),

                Section::make('Statistics')
                    ->description('Shown as the KPI row under the About hero (e.g. "250+ Projects Completed").')
                    ->visible(fn (Get $get) => in_array($get('section_key'), self::STATS_SECTIONS))
                    ->columnSpanFull()
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

                Section::make('Section Image')
                    ->visible(fn (Get $get) => in_array($get('section_key'), self::IMAGE_SECTIONS))
                    ->columnSpanFull()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('page_section_image')
                            ->collection('page_section_image')
                            ->imagePreviewHeight('200')
                            ->preserveFilenames(),
                    ]),

                Section::make('Gallery')
                    ->description('Multiple photos, e.g. the workshop/production facility gallery.')
                    ->visible(fn (Get $get) => in_array($get('section_key'), self::GALLERY_SECTIONS))
                    ->columnSpanFull()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('page_section_gallery')
                            ->collection('page_section_gallery')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->maxFiles(10)
                            ->preserveFilenames(),
                    ]),
            ]);
    }
}
