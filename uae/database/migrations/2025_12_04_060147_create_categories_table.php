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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Images
            $table->string('banner_image')->nullable();
            $table->string('icon_image')->nullable();
            $table->string('cover_image')->nullable();

            // Image ALT (important for SEO)
            $table->string('banner_alt')->nullable();
            $table->string('icon_alt')->nullable();
            $table->string('cover_alt')->nullable();

            // SEO META
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            // Open Graph (for WhatsApp / FB / LinkedIn)
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
