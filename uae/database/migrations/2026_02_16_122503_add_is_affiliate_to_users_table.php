<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 🚀 user_type enum को अपडेट करना ताकि 'affiliate' शामिल हो सके
            DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'staff', 'seller', 'customer', 'affiliate') NOT NULL DEFAULT 'customer'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 🔙 वापस पुराने स्टेट पर जाने के लिए
            DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'staff', 'seller', 'customer') NOT NULL DEFAULT 'customer'");
        });
    }
};
