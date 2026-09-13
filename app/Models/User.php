<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\LogsActivity;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, LogsActivity;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_CONTENT_EDITOR = 'content_editor';
    public const ROLE_HR_MANAGER = 'hr_manager';

    public const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_CONTENT_EDITOR => 'Content Editor',
        self::ROLE_HR_MANAGER => 'HR Manager',
    ];

    /**
     * BRD §8's permission matrix: module => role => access level.
     * 'full' = create/edit/publish/delete, 'edit' = create/edit/publish,
     * 'view' = read-only, 'none' = hidden entirely.
     *
     * Modules beyond the BRD's four (Homepage, Blogs, Services) are bucketed
     * under 'content_management' with the same profile as Project/About Us,
     * since they are the same kind of marketing-team content.
     */
    public const MODULE_LEVELS = [
        'user_management' => [
            self::ROLE_SUPER_ADMIN => 'full',
            self::ROLE_CONTENT_EDITOR => 'none',
            self::ROLE_HR_MANAGER => 'none',
        ],
        'project_management' => [
            self::ROLE_SUPER_ADMIN => 'full',
            self::ROLE_CONTENT_EDITOR => 'edit',
            self::ROLE_HR_MANAGER => 'view',
        ],
        'product_gallery' => [
            self::ROLE_SUPER_ADMIN => 'full',
            self::ROLE_CONTENT_EDITOR => 'edit',
            self::ROLE_HR_MANAGER => 'view',
        ],
        'about_us' => [
            self::ROLE_SUPER_ADMIN => 'full',
            self::ROLE_CONTENT_EDITOR => 'edit',
            self::ROLE_HR_MANAGER => 'view',
        ],
        'content_management' => [
            self::ROLE_SUPER_ADMIN => 'full',
            self::ROLE_CONTENT_EDITOR => 'edit',
            self::ROLE_HR_MANAGER => 'view',
        ],
        'careers_postings' => [
            self::ROLE_SUPER_ADMIN => 'full',
            self::ROLE_CONTENT_EDITOR => 'view',
            self::ROLE_HR_MANAGER => 'edit',
        ],
        'careers_applications' => [
            self::ROLE_SUPER_ADMIN => 'full',
            self::ROLE_CONTENT_EDITOR => 'none',
            self::ROLE_HR_MANAGER => 'edit',
        ],
        // FR-1.4: activity log is Super Admin only, same shape as user_management.
        'activity_log' => [
            self::ROLE_SUPER_ADMIN => 'full',
            self::ROLE_CONTENT_EDITOR => 'none',
            self::ROLE_HR_MANAGER => 'none',
        ],
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /** Access level ('full'/'edit'/'view'/'none') this user has for a module. */
    public function accessLevel(string $module): string
    {
        return self::MODULE_LEVELS[$module][$this->role] ?? 'none';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
