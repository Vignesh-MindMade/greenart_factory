<?php

namespace App\Filament\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Gates a resource's CRUD actions by the current user's role, per BRD §8's
 * permission matrix (see User::MODULE_LEVELS).
 *
 * A resource using this trait must declare:
 *     public const MODULE = 'project_management';
 *
 * Resources that already hardcode canCreate()/canDelete() for structural
 * reasons (singleton content, API-only creation) keep those overrides — a
 * class's own method always wins over a trait's — so this only fills in
 * what the resource doesn't already define itself.
 */
trait HasModuleAccess
{
    public static function canViewAny(): bool
    {
        return static::userLevel() !== 'none';
    }

    public static function canCreate(): bool
    {
        return in_array(static::userLevel(), ['edit', 'full'], true);
    }

    public static function canEdit($record): bool
    {
        return in_array(static::userLevel(), ['edit', 'full'], true);
    }

    public static function canDelete($record): bool
    {
        return static::userLevel() === 'full';
    }

    public static function canDeleteAny(): bool
    {
        return static::userLevel() === 'full';
    }

    /** Restoring/force-deleting a trashed record is as destructive as deleting it. */
    public static function canRestore($record): bool
    {
        return static::userLevel() === 'full';
    }

    public static function canRestoreAny(): bool
    {
        return static::userLevel() === 'full';
    }

    public static function canForceDelete($record): bool
    {
        return static::userLevel() === 'full';
    }

    public static function canForceDeleteAny(): bool
    {
        return static::userLevel() === 'full';
    }

    protected static function userLevel(): string
    {
        $user = Auth::user();

        return $user instanceof User ? $user->accessLevel(static::MODULE) : 'none';
    }
}
