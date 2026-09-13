<?php

namespace App\Filament\Resources\JobApplications;

use App\Filament\Resources\JobApplications\Pages\EditJobApplication;
use App\Filament\Resources\JobApplications\Pages\ListJobApplications;
use App\Filament\Resources\JobApplications\Schemas\JobApplicationForm;
use App\Filament\Resources\JobApplications\Tables\JobApplicationsTable;
use App\Models\JobApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|UnitEnum|null $navigationGroup = 'Careers';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    // Applications only ever arrive through the public /api/careers apply endpoint.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return JobApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicationsTable::configure($table);
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
            'index' => ListJobApplications::route('/'),
            'edit' => EditJobApplication::route('/{record}/edit'),
        ];
    }
}
