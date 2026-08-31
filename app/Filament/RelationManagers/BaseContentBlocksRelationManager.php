<?php

namespace App\Filament\RelationManagers;

use App\Models\ContentBlock;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Manages the ordered lists attached to a page section. Today that is the
 * "Why Choose Us" items on the products section; the same manager serves any
 * future group without modification.
 */
abstract class BaseContentBlocksRelationManager extends RelationManager
{
    protected static string $relationship = 'contentBlocks';

    protected static ?string $title = 'Content blocks';

    /**
     * Group new rows default to, and the only group this manager lists.
     * Null shows every group — used where a parent has more than one list.
     */
    protected static ?string $group = null;


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('group')
                    ->required()
                    ->options(ContentBlock::GROUPS)
                    ->default(fn () => static::$group ?? ContentBlock::GROUP_WHY_CHOOSE_US)
                    // Fixed when the manager is scoped to a single group.
                    ->visible(fn (): bool => static::$group === null)
                    ->dehydratedWhenHidden()
                    ->helperText('Which list on the page this item belongs to.'),

                Grid::make(2)
                    ->schema([
                        TextInput::make('display_no')
                            ->label('Number')
                            ->maxLength(8)
                            ->placeholder('01')
                            ->helperText('Large numeral beside the title. Leave blank for unnumbered lists.'),
                        TextInput::make('sort_order')
                            ->label('Order')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Exceptional Craftsmanship')
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->rows(3)
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        TextInput::make('value')
                            ->label('Headline figure')
                            ->maxLength(255)
                            ->placeholder('98%')
                            ->helperText('Only used by stat-style lists. Leave blank otherwise.'),
                        Select::make('status')
                            ->required()
                            ->default('draft')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->helperText('Only published items appear on the site.'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(fn (Builder $query) => static::$group
                ? $query->where('group', static::$group)
                : $query)
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('display_no')
                    ->label('#')
                    ->badge(),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('description')
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('group')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContentBlock::GROUPS[$state] ?? $state)
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'archived'  => 'danger',
                        default     => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(ContentBlock::GROUPS),
                SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
