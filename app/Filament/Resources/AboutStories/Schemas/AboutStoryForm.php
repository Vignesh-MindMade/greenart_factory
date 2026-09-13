<?php

namespace App\Filament\Resources\AboutStories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AboutStoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tab_label')
                    ->required()
                    ->helperText('Shown as the tab label, e.g. "Our Story", "Mission", "Vision"'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true),
                RichEditor::make('content')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->required()
                    ->default('draft')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('about_story_images')
                            ->collection('about_story_images')
                            ->imagePreviewHeight('200')
                            ->preserveFilenames(),
                    ]),
            ]);
    }
}
