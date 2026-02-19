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
        // 1. Add JSON column to Products
        Schema::table('products', function (Blueprint $table) {
            $table->json('faq_content')->nullable()->after('story_content');
        });

        // 2. Add JSON column to Home Page Settings
        Schema::table('home_page_settings', function (Blueprint $table) {
            $table->json('faq_content')->nullable()->after('story_content');
        });

        // 3. Create General FAQs Table (For the main FAQ page)
        Schema::create('general_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_faqs');
    }
};
