<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memindahkan status_kelayakan dan status_validasi dari tabel diklat ke list_jadwal_diklat.
     * Idempotent — menggunakan hasColumn checks agar aman untuk fresh migration maupun incremental.
     */
    public function up(): void
    {
        // Tambahkan status_kelayakan dan status_validasi ke list_jadwal_diklat jika belum ada
        Schema::table('list_jadwal_diklat', function (Blueprint $table) {
            if (! Schema::hasColumn('list_jadwal_diklat', 'status_kelayakan')) {
                $table->enum('status_kelayakan', ['layak', 'tidak layak'])->nullable()->after('status_diklat');
            }
            if (! Schema::hasColumn('list_jadwal_diklat', 'status_validasi')) {
                $table->enum('status_validasi', ['valid', 'tidak valid'])->nullable()->after('status_kelayakan');
            }
        });

        // Hapus kolom status dari diklat jika masih ada (kasus incremental migrate)
        if (Schema::hasColumn('diklat', 'status_kelayakan') || Schema::hasColumn('diklat', 'status_validasi')) {
            Schema::table('diklat', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('diklat', 'status_kelayakan')) {
                    $cols[] = 'status_kelayakan';
                }
                if (Schema::hasColumn('diklat', 'status_validasi')) {
                    $cols[] = 'status_validasi';
                }
                if ($cols) {
                    $table->dropColumn($cols);
                }
            });
        }
    }

    public function down(): void
    {
        // Rollback: hapus kolom dari list_jadwal_diklat
        Schema::table('list_jadwal_diklat', function (Blueprint $table) {
            if (Schema::hasColumn('list_jadwal_diklat', 'status_kelayakan')) {
                $table->dropColumn('status_kelayakan');
            }
            if (Schema::hasColumn('list_jadwal_diklat', 'status_validasi')) {
                $table->dropColumn('status_validasi');
            }
        });

        // Rollback: kembalikan kolom ke diklat jika belum ada
        Schema::table('diklat', function (Blueprint $table) {
            if (! Schema::hasColumn('diklat', 'status_kelayakan')) {
                $table->enum('status_kelayakan', ['layak', 'tidak layak'])->nullable()->after('nama_kegiatan');
            }
            if (! Schema::hasColumn('diklat', 'status_validasi')) {
                $table->enum('status_validasi', ['valid', 'tidak valid'])->nullable()->after('status_kelayakan');
            }
        });
    }
};
