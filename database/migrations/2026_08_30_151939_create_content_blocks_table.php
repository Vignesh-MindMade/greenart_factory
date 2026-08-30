<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One shared table for every "ordered list of title + description attached
     * to a parent" in the design. The Figma audit found nine of these with an
     * identical shape:
     *
     *   why_choose_us   products page      title + description, numbered
     *   benefit         careers page       title + description
     *   process         careers / services numbered step + timing note
     *   faq             service detail     question + answer
     *   feature         service detail     short capability chips
     *   value           about us           core values
     *   stat            about us           value ("250+") + label
     *   certification   about us           code + subtitle + description
     *   badge           about us           trust cards
     *
     * Building them as nine tables would mean nine migrations, nine models,
     * nine Filament resources and nine places to fix the same bug. Only
     * why_choose_us is wired up so far; the rest attach as their pages are built.
     */
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->morphs('blockable');
            // Which list on the parent this block belongs to.
            $table->string('group', 40);

            // Large numeral or step marker shown beside the title ("01").
            $table->string('display_no', 8)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            // Headline figure for stat-style blocks ("98%", "Within 2 weeks").
            $table->string('value')->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();

            // Every read is "blocks of group X for parent Y, in order".
            $table->index(['blockable_type', 'blockable_id', 'group', 'sort_order'], 'content_blocks_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
