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
}
