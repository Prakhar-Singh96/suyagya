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
        Schema::create('user_details', function (Blueprint $table) {
           $table->id(); // BigInt PK

            // Foreign Key linking to users table
            // 'constrained' ka matlab hai ye users table ki id check karega
            // 'onDelete cascade' ka matlab agar User delete hua to Details bhi delete ho jayengi
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('avatar')->nullable(); // Photo can be empty initially
            $table->string('phone')->nullable();
            $table->text('address')->nullable(); // Text is better for long address
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            // Seller Specific Fields (Must be nullable because Admin/Customer won't have them)
            $table->string('shop_name')->nullable();
            $table->string('gst_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
