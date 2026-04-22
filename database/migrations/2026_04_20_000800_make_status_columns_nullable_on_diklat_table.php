<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE diklat MODIFY status_kelayakan ENUM('layak', 'tidak layak') NULL");
        DB::statement("ALTER TABLE diklat MODIFY status_validasi ENUM('valid', 'tidak valid') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE diklat MODIFY status_kelayakan ENUM('layak', 'tidak layak') NOT NULL");
        DB::statement("ALTER TABLE diklat MODIFY status_validasi ENUM('valid', 'tidak valid') NOT NULL");
    }
};
