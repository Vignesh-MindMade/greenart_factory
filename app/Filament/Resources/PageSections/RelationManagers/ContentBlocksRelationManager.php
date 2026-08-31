<?php

namespace App\Filament\Resources\PageSections\RelationManagers;

use App\Filament\RelationManagers\BaseContentBlocksRelationManager;

/**
 * Page sections can carry more than one list, so this one is unscoped —
 * the group picker stays visible.
 */
class ContentBlocksRelationManager extends BaseContentBlocksRelationManager
{
}
