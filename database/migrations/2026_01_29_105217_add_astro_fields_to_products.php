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
        Schema::table('products', function (Blueprint $table) {
            $table->string('astro_planet')->nullable(); // जैसे: Jupiter, Mars
            $table->string('astro_rashi')->nullable();  // जैसे: Aries, Leo
            $table->text('astro_benefits')->nullable(); // संक्षिप्त फायदे
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['astro_planet', 'astro_rashi', 'astro_benefits']);
        });
    }
};
