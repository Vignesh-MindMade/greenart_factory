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
        Schema::table('page_sections', function (Blueprint $table) {
            $table->string('stat_1_value')->nullable();
            $table->string('stat_1_label')->nullable();
            $table->string('stat_2_value')->nullable();
            $table->string('stat_2_label')->nullable();
            $table->string('stat_3_value')->nullable();
            $table->string('stat_3_label')->nullable();
            $table->string('stat_4_value')->nullable();
            $table->string('stat_4_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropColumn([
                'stat_1_value', 'stat_1_label',
                'stat_2_value', 'stat_2_label',
                'stat_3_value', 'stat_3_label',
                'stat_4_value', 'stat_4_label',
            ]);
        });
    }
};
