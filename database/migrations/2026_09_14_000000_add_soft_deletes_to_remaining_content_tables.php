<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'portfolio_categories',
        'locations',
        'sectors',
        'installation_types',
        'products',
        'services',
        'blog_categories',
        'blog_posts',
        'hero_slides',
        'testimonials',
        'partners',
        'team_members',
        'about_stories',
        'certifications',
        'certification_badges',
        'core_values',
        'job_postings',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropSoftDeletes();
            });
        }
    }
};
