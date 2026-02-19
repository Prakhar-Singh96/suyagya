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
            // MRP Price column (after description)
            $table->decimal('mrp_price', 10, 2)->nullable()->after('description');

            // Discount Percentage column (after mrp_price)
            // Default 0 rakha hai taki calculation me error na aaye
            $table->integer('discount')->default(0)->after('mrp_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['mrp_price', 'discount']);
        });
    }
};
