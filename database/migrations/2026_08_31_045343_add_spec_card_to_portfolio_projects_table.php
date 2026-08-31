<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The dark specification card beside the challenge & solution block on the
     * project detail screen (Figma node 640:2219).
     *
     * Only the singular fields live here. The card's label/value rows — Area,
     * Location, System, Install Year, Client, Moss Coverage, Plant Species —
     * are content_blocks with group 'project_spec', since that is exactly the
     * shape that table exists for.
     */
    public function up(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            // "MOSS WALL INSTALLATION – COMMERCIAL PROJECT"
            $table->string('spec_eyebrow')->nullable()->after('solution');
            // "This bespoke moss wall creates a calming green backdrop..."
            $table->text('spec_headline')->nullable()->after('spec_eyebrow');
            // The two paragraphs below the divider.
            $table->longText('spec_body')->nullable()->after('spec_headline');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropColumn(['spec_eyebrow', 'spec_headline', 'spec_body']);
        });
    }
};
