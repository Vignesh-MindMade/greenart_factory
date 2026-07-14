<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;


class BlogPostsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               
                  Grid::make(2)->schema([
                    TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]),
             Select::make('blog_category_id')
                ->label('Blog')
                ->relationship('blogs', 'name')
                   ->searchable()
                ->preload()
                ->required()
                ->createOptionForm([
                    // Inline create — admin can add a new category without leaving the form
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, $set) =>
                            $set('slug', Str::slug($state))
                        ),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique('blog_categories', 'slug'),
                ]),
                Textarea::make('excerpt')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Short summary shown on blog listing cards (max 500 chars)')
                    ->columnSpanFull(),
                  RichEditor::make('content')
                ->columnSpanFull()
                ->toolbarButtons([
                    'bold', 'italic', 'underline', 'strike',
                    'h2', 'h3',
                    'bulletList', 'orderedList',
                    'blockquote', 'link',
                    'undo', 'redo',
                ]),
                  Grid::make(2)->schema([
                Select::make('status')
                    ->required()
                    ->default('draft')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ]),

                DateTimePicker::make('published_at')
                    ->nullable()
                    ->label('Schedule Publish')
                    ->helperText('Leave empty to publish immediately when status is set to Published'),
            ]),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                // TextInput::make('status')
                //     ->required()
                //     ->default('draft'),

                  Section::make('Cover Image')->schema([
                SpatieMediaLibraryFileUpload::make('cover_image')
                    ->collection('cover_image')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imagePreviewHeight('250')
                    ->preserveFilenames()
                    ->label('Cover Image (used on blog listing cards)'),
            ]),
            ]);
    }
}
