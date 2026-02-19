<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Home Page Settings Table (With Full SEO)
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();

            // Brand Story / Q&A Section
            $table->string('story_title')->nullable();
            $table->longText('story_content')->nullable();

            // Full SEO Fields
            $table->string('meta_title')->nullable();        // SEO Title
            $table->text('meta_description')->nullable();    // SEO Description
            $table->string('meta_keywords')->nullable();     // Keywords
            $table->string('og_image')->nullable();          // Social Share Image

            $table->timestamps();
        });

        // 2. Add columns to Categories
        Schema::table('categories', function (Blueprint $table) {
            // Check if column exists to avoid error during re-run
            if (!Schema::hasColumn('categories', 'story_title')) {
                $table->string('story_title')->nullable()->after('status');
                $table->longText('story_content')->nullable()->after('story_title');
            }
        });

        // 3. Add columns to Sub Categories
        Schema::table('sub_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_categories', 'story_title')) {
                $table->string('story_title')->nullable()->after('status');
                $table->longText('story_content')->nullable()->after('story_title');
            }
        });

        // 4. Add columns to Products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'story_title')) {
                $table->string('story_title')->nullable()->after('description');
                $table->longText('story_content')->nullable()->after('story_title');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('home_page_settings');
        // Drop columns logic can be added here
    }
};
