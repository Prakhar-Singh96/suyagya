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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained()->onDelete('set null');

            // Basic Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);

            // Images
            $table->string('main_image')->nullable();

            // ALT Tag (for SEO + accessibility)
            $table->string('main_image_alt')->nullable();

            // SEO META
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            // Open Graph for Social Sharing
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            // Extra (optional but powerful for ecommerce)
            $table->string('sku')->nullable();
            $table->decimal('weight', 8, 2)->nullable();

            $table->boolean('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
