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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            // If user is logged in, you might want user_id, but you asked for manual email/name fields
            $table->unsignedBigInteger('user_id')->nullable(); 

            $table->integer('rating'); // 1 to 5
            $table->string('title')->nullable(); // Review Title
            $table->text('review'); // Feedback content
            
            // Media can be stored as JSON array of paths e.g., ["reviews/img1.jpg", "reviews/vid1.mp4"]
            $table->json('media')->nullable(); 
            
            $table->string('display_name'); // Public name
            $table->string('email'); // For verification/contact
            
            // Status: 0 = Pending, 1 = Approved, 2 = Rejected
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
