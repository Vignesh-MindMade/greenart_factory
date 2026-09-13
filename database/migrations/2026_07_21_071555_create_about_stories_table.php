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
        Schema::create('about_stories', function (Blueprint $table) {
            $table->id();
            $table->string('tab_label');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->tinyInteger('sort_order')->default(0);
             $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_stories');
    }
};
