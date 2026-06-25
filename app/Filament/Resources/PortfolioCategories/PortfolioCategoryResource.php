<?php

namespace App\Filament\Resources\PortfolioCategories;

use App\Filament\Resources\PortfolioCategories\Pages\CreatePortfolioCategory;
use App\Filament\Resources\PortfolioCategories\Pages\EditPortfolioCategory;
use App\Filament\Resources\PortfolioCategories\Pages\ListPortfolioCategories;
use App\Filament\Resources\PortfolioCategories\Schemas\PortfolioCategoryForm;
use App\Filament\Resources\PortfolioCategories\Tables\PortfolioCategoriesTable;
use App\Models\PortfolioCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortfolioCategoryResource extends Resource
{
    protected static ?string $model = PortfolioCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PortfolioCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortfolioCategoriesTable::configure($table);
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
            'index' => ListPortfolioCategories::route('/'),
            'create' => CreatePortfolioCategory::route('/create'),
            'edit' => EditPortfolioCategory::route('/{record}/edit'),
        ];
    }
}
