<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key_id')->nullable();
            $table->string('key_secret')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        // Default Row Insert karein
        DB::table('payment_settings')->insert([
            'key_id' => 'rzp_test_RqyLdelrr8WCcc',
            'key_secret' => '3g3Kc0tKNtnDtqxOqApk5HFI',
            'is_active' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
