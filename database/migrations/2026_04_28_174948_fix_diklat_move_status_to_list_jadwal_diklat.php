<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memindahkan status_kelayakan dan status_validasi dari tabel diklat ke list_jadwal_diklat.
     * Juga menghapus kolom yang tidak relevan dari diklat, dan menambah sertif_file_path / no_sertif
     * yang sebelumnya hilang dari list_jadwal_diklat.
     */
    public function up(): void
    {
        // 1. Tambahkan status_kelayakan dan status_validasi ke list_jadwal_diklat
        Schema::table('list_jadwal_diklat', function (Blueprint $table) {
            $table->enum('status_kelayakan', ['layak', 'tidak layak'])->nullable()->after('status_diklat');
            $table->enum('status_validasi', ['valid', 'tidak valid'])->nullable()->after('status_kelayakan');
        });

        // 2. Hapus kolom yang sudah dipindah dari diklat
        Schema::table('diklat', function (Blueprint $table) {
            $table->dropColumn(['status_kelayakan', 'status_validasi']);
        });
    }

    public function down(): void
    {
        // Rollback: hapus kolom dari list_jadwal_diklat
        Schema::table('list_jadwal_diklat', function (Blueprint $table) {
            $table->dropColumn(['status_kelayakan', 'status_validasi']);
        });

        // Rollback: kembalikan kolom ke diklat
        Schema::table('diklat', function (Blueprint $table) {
            $table->enum('status_kelayakan', ['layak', 'tidak layak'])->nullable()->after('nama_kegiatan');
            $table->enum('status_validasi', ['valid', 'tidak valid'])->nullable()->after('status_kelayakan');
        });
    }
};
