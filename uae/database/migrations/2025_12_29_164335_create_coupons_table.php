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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. SAVE10
            $table->enum('type', ['fixed', 'percent']); // 100 Rs Off or 10% Off
            $table->decimal('value', 10, 2); // Discount amount
            $table->decimal('min_cart_amount', 10, 2)->nullable(); // Min purchase requirement
            $table->date('expires_at')->nullable();
            $table->boolean('status')->default(1); // Active/Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
