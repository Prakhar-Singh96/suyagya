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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('awb_number')->nullable()->after('status'); // Tracking Number
            $table->string('courier_name')->nullable()->after('awb_number'); // e.g. BlueDart, Delhivery
            $table->string('tracking_url')->nullable()->after('courier_name'); // Direct Link
            $table->date('expected_delivery_date')->nullable()->after('tracking_url'); // Kab tak aayega
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['awb_number', 'courier_name', 'tracking_url', 'expected_delivery_date']);
        });
    }
};
