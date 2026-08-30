<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Editable copy for the varieties section header on the product detail page.
     * Per-product, since each product names its varieties differently
     * ("Choose Your Texture & Feel" for moss, something else for trees).
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('varieties_title')->nullable()->after('description');
            $table->text('varieties_intro')->nullable()->after('varieties_title');
            $table->string('varieties_footer')->nullable()->after('varieties_intro');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['varieties_title', 'varieties_intro', 'varieties_footer']);
        });
    }
};
