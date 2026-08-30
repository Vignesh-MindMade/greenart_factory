<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fields for the product detail page: each variant renders as a numbered
     * block with a description and a specification card.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('slug');
            // Display label shown alongside the block heading ("01", "02").
            // Not a sequence — the design repeats values, so it is free text.
            $table->string('display_no', 8)->nullable()->after('description');
            // Specification card: one label/value pair plus attribute chips.
            $table->string('spec_label')->nullable()->after('display_no');
            $table->string('spec_value')->nullable()->after('spec_label');
            $table->json('spec_tags')->nullable()->after('spec_value');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('spec_tags');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'display_no',
                'spec_label',
                'spec_value',
                'spec_tags',
                'sort_order',
            ]);
        });
    }
};
