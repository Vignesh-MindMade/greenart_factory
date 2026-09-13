<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Default super_admin so the existing seeded account isn't locked
            // out of anything when this column lands (BRD §9: a single Super
            // Admin exists at launch; more roles are added after).
            $table->enum('role', ['super_admin', 'content_editor', 'hr_manager'])
                ->default('super_admin')
                ->after('email');
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active']);
        });
    }
};
