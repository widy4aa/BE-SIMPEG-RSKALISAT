<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom created_by sudah dimasukkan ke migration utama 2026_04_07_000100_create_hris_tables.
     * Migration ini dipertahankan agar history tidak rusak, namun tidak melakukan apa-apa.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('diklat', 'created_by')) {
            Schema::table('diklat', function (Blueprint $table) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->after('kategori_diklat_id')
                    ->constrained('pegawai')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('diklat', 'created_by')) {
            Schema::table('diklat', function (Blueprint $table) {
                $table->dropConstrainedForeignId('created_by');
            });
        }
    }
};
