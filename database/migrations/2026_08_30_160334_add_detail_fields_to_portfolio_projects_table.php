<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Body copy for the project detail screen (Figma node 640:2214).
     *
     * The table previously stored title, slug, status, two FKs and the two
     * homepage-featuring columns — no descriptive text at all, while the
     * detail screen is almost entirely prose.
     *
     * challenge and solution are separate columns even though the design
     * renders them as one "our challenge & solution" block, because BRD FR-2.3
     * asks for "dedicated structured fields". The frontend can concatenate;
     * splitting them later would be far harder.
     */
    public function up(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            // Card blurb on the portfolio listing.
            $table->text('excerpt')->nullable()->after('slug');
            // "Our execution" — intro paragraph.
            $table->longText('execution')->nullable()->after('excerpt');
            // "Key stages included: ..." — bulleted body.
            $table->longText('key_stages')->nullable()->after('execution');
            // "Key highlights" — bulleted body.
            $table->longText('key_highlights')->nullable()->after('key_stages');
            // BRD FR-2.3.
            $table->longText('challenge')->nullable()->after('key_highlights');
            $table->longText('solution')->nullable()->after('challenge');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropColumn([
                'excerpt',
                'execution',
                'key_stages',
                'key_highlights',
                'challenge',
                'solution',
            ]);
        });
    }
};
