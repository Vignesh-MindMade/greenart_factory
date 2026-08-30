<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Publish control for the product detail page blocks and the varieties grid.
     * Default is 'draft' to match every other content table, but existing rows
     * are backfilled to 'published' — they are already live on the site, and
     * leaving them at the default would empty every product detail page.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('sort_order');
        });

        Schema::table('product_varieties', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('sort_order');
        });

        DB::table('product_variants')->update(['status' => 'published']);
        DB::table('product_varieties')->update(['status' => 'published']);
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('product_varieties', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
