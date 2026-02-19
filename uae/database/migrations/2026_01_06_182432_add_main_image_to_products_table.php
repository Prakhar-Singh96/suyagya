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
            // 'main_image' के बाद नए कॉलम जोड़ रहे हैं
            $table->string('product_main_image')->nullable()->after('main_image');
            $table->string('product_main_image_alt')->nullable()->after('product_main_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_main_image', 'product_main_image_alt']);
        });
    }
};
