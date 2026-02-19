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
            $table->string('system_order_id')->nullable()->after('id'); // BigShip ID
            $table->text('shipment_label_url')->nullable()->after('tracking_url'); // Label PDF Link
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['system_order_id', 'shipment_label_url']);
        });
    }
};
