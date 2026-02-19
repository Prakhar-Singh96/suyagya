<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // 🔥 Ye line add karna mat bhulna

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY meta_keywords TEXT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Agar wapas roll back karna ho to VARCHAR(255) bana do
        DB::statement("ALTER TABLE products MODIFY meta_keywords VARCHAR(255) NULL");
    }
};
