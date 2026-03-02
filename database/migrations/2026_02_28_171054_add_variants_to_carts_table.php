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
        Schema::table('carts', function (Blueprint $table) {
            // variant_id को product_id के बाद जोड़ेंगे
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            $table->string('ring_size')->nullable()->after('variant_id');

            // अगर आप विदेशी कुंजी (Foreign Key) जोड़ना चाहते हैं (Optional but recommended)
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['variant_id']); // पहले foreign key हटाएं
            $table->dropColumn(['variant_id', 'ring_size']);
        });
    }
};
