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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Video ka Title
            $table->string('link')->nullable();  // 'Buy Now' button ka link
            $table->string('video');             // Video file path
            $table->string('image')->nullable(); // Poster/Thumbnail image
            $table->boolean('status')->default(1); // 1 = Active, 0 = Inactive
            $table->integer('sort_order')->default(0); // Display order
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
