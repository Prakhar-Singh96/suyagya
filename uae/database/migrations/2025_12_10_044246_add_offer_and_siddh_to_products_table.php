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
            // 1. Offer Timer (Kab khatam hoga)
            $table->timestamp('offer_end_time')->nullable();

            // 2. Siddh Version Add-on
            $table->boolean('is_siddh_enabled')->default(0); // Kya ye feature on hai?
            $table->decimal('siddh_price', 10, 2)->default(0); // Kitna rupya extra lagega?
            $table->boolean('emi_available')->default(0); // 0 = No, 1 = Yes
            $table->integer('delivery_days')->default(7); // Default 7 days
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['offer_end_time', 'is_siddh_enabled', 'siddh_price', 'delivery_days', 'emi_available']);
        });
    }
};
