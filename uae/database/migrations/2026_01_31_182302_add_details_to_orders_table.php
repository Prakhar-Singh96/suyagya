<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('mrp_total', 10, 2)->default(0)->after('total_amount');
            $table->decimal('coupon_discount', 10, 2)->default(0)->after('mrp_total');
            $table->decimal('gaming_discount', 10, 2)->default(0)->after('coupon_discount');
            $table->string('coupon_code')->nullable()->after('gaming_discount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('ring_size')->nullable()->after('is_siddh');
            $table->decimal('siddh_amount', 10, 2)->default(0)->after('ring_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🚀 रोलबैक के लिए कॉलम्स हटाना ज़रूरी है
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['mrp_total', 'coupon_discount', 'gaming_discount', 'coupon_code']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['ring_size', 'siddh_amount']);
        });
    }
};
