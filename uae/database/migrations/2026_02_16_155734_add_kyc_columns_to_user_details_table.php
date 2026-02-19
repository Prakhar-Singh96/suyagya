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
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('pan_card_no')->nullable()->after('gst_number');
            $table->string('aadhar_card_no')->nullable()->after('pan_card_no');
            $table->string('bank_name')->nullable()->after('aadhar_card_no');
            $table->string('account_no')->nullable()->after('bank_name');
            $table->string('ifsc_code')->nullable()->after('account_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn(['pan_card_no', 'aadhar_card_no', 'bank_name', 'account_no', 'ifsc_code']);
        });
    }
};
