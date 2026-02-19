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
        Schema::create('gemstone_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->string('type'); // ring, pendant, loose
            $table->string('ratti_size')->nullable(); // 4.5, 5.25
            $table->string('material')->nullable(); // silver, panchdhatu
            $table->string('ring_size')->nullable(); // 12, 14, 20

            $table->decimal('price', 10, 2); // Final Selling Price
            $table->decimal('mrp', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gemstone_variants');
    }
};
