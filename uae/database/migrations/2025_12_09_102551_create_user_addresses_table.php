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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('name'); // Receiver Name (User name se alag ho sakta hai)
            $table->string('phone'); // Receiver Phone

            $table->string('pincode');
            $table->string('address_line1'); // House No, Building
            $table->string('address_line2')->nullable(); // Street, Area
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('India');

            $table->string('type')->default('home'); // home, work, warehouse (for vendor)
            $table->boolean('is_default')->default(false); // Default address marker
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
