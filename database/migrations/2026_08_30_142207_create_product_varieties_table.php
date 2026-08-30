<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The "Choose Your Texture & Feel" grid on the product detail page —
     * e.g. Flat Moss / Reindeer Moss / Pole Moss / Mixed Moss under Moss Creations.
     * Sits below the variant blocks and belongs to the product, not the variant.
     */
    public function up(): void
    {
        Schema::create('product_varieties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            // Eyebrow above the name ("Variety 01").
            $table->string('label')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            // Slug is unique per product, matching the product_variants pattern.
            $table->unique(['product_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_varieties');
    }
};
