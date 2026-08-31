<?php

namespace App\Filament\Resources\PortfolioProjects\RelationManagers;

use App\Filament\RelationManagers\BaseContentBlocksRelationManager;
use App\Models\ContentBlock;

/**
 * The label/value rows on the project detail specification card —
 * Area, Location, System, Install Year, Client, and so on.
 *
 * Scoped to one group, so the group picker is hidden and new rows land in
 * project_spec automatically.
 */
class ProjectSpecsRelationManager extends BaseContentBlocksRelationManager
{
    protected static ?string $title = 'Specification rows';

    protected static ?string $group = ContentBlock::GROUP_PROJECT_SPEC;

    /**
     * Location already exists on the project as a foreign key, so its value is
     * derived. Must stay in step with ProjectDetailResource::autoValue().
     */
    protected static array $autoFilledLabels = ['Location'];

    protected static function valueHelperText(): string
    {
        return 'The value shown on the card, e.g. "60 m²".';
    }
}
