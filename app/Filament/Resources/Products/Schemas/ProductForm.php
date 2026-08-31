<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required() ->maxLength(255)
                    ->live(onBlur: true)           // fires the callback when admin clicks away
                    ->afterStateUpdated(function (string $operation, ?string $state, $set) {
                    if ($operation === 'create') {  // only auto-fill on CREATE, not EDIT
                        $set('slug', Str::slug($state));
                    }
                    // On edit: admin may have custom slug — don't overwrite it
                }),
                TextInput::make('slug')
                    ->required() ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Textarea::make('description')
                ->rows(3)
                ->maxLength(1000)
                ->helperText('Short summary shown on product category (max 500 chars)')
                    ->columnSpanFull(),
                    
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
                        SpatieMediaLibraryFileUpload::make('cover_image')
                            ->collection('cover_image')
                            ->imagePreviewHeight('200')

                            ->preserveFilenames(),
                    ]),

                Section::make('Gallery page')
                    ->description('Drives /gallery/' . '{slug}. Project images flow in automatically — these are the extras.')
                    ->collapsed()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('gallery_hero')
                            ->collection('gallery_hero')
                            ->label('Hero banner')
                            ->image()
                            ->imagePreviewHeight('150')
                            ->preserveFilenames()
                            ->helperText('Wide banner at the top of the gallery page.'),
                        SpatieMediaLibraryFileUpload::make('product_gallery')
                            ->collection('product_gallery')
                            ->label('Standalone gallery images')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->maxFiles(40)
                            ->preserveFilenames()
                            ->helperText('Images not tied to any project. Images from published projects linked to this collection appear automatically and do not need re-uploading.'),
                    ]),

                Section::make('Varieties section')
                    ->description('Header copy for the texture grid on the product detail page. The varieties themselves are managed in the tab below, after saving.')
                    ->collapsed()
                    ->schema([
                        TextInput::make('varieties_title')
                            ->label('Section title')
                            ->maxLength(255)
                            ->placeholder('Choose Your Texture & Feel'),
                        Textarea::make('varieties_intro')
                            ->label('Section intro')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Each moss type brings a distinct character…')
                            ->columnSpanFull(),
                        TextInput::make('varieties_footer')
                            ->label('Closing caption')
                            ->maxLength(255)
                            ->placeholder('Choose your favourite texture'),
                    ]),
            ]);
    }
}
