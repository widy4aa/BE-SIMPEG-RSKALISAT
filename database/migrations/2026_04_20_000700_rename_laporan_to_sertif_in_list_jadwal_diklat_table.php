<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('list_jadwal_diklat', 'sertif_file_path')) {
            Schema::table('list_jadwal_diklat', function (Blueprint $table) {
                $table->string('sertif_file_path')->nullable()->after('pegawai_id');
            });
        }

        if (Schema::hasColumn('list_jadwal_diklat', 'laporan_file_path')) {
            DB::table('list_jadwal_diklat')
                ->whereNull('sertif_file_path')
                ->update([
                    'sertif_file_path' => DB::raw('laporan_file_path'),
                ]);

            Schema::table('list_jadwal_diklat', function (Blueprint $table) {
                $table->dropColumn('laporan_file_path');
            });
        }

        if (!Schema::hasColumn('list_jadwal_diklat', 'no_sertif')) {
            Schema::table('list_jadwal_diklat', function (Blueprint $table) {
                $table->string('no_sertif')->nullable()->after('sertif_file_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('list_jadwal_diklat', 'laporan_file_path')) {
            Schema::table('list_jadwal_diklat', function (Blueprint $table) {
                $table->string('laporan_file_path')->nullable()->after('pegawai_id');
            });
        }

        if (Schema::hasColumn('list_jadwal_diklat', 'sertif_file_path')) {
            DB::table('list_jadwal_diklat')
                ->whereNull('laporan_file_path')
                ->update([
                    'laporan_file_path' => DB::raw('sertif_file_path'),
                ]);

            Schema::table('list_jadwal_diklat', function (Blueprint $table) {
                $table->dropColumn('sertif_file_path');
            });
        }

        if (Schema::hasColumn('list_jadwal_diklat', 'no_sertif')) {
            Schema::table('list_jadwal_diklat', function (Blueprint $table) {
                $table->dropColumn('no_sertif');
            });
        }
    }
};
