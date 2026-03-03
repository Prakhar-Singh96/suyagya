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
            // डिस्क्रिप्शन के बाद नया कॉलम
            $table->json('product_tabs')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // जो कॉलम हमने जोड़ा था, उसे यहाँ ड्रॉप (डिलीट) करेंगे
            $table->dropColumn('product_tabs');
        });
    }
};
