<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * status_kelayakan dan status_validasi sudah dipindah dari diklat ke list_jadwal_diklat
     * via migration 2026_04_28_174948. Migration ini tidak melakukan apa-apa agar idempotent.
     */
    public function up(): void
    {
        // Kolom status sudah tidak ada di tabel diklat (sudah dipindah ke list_jadwal_diklat).
        // Tidak ada aksi yang diperlukan.
    }

    public function down(): void
    {
        // Tidak ada rollback karena kolom sudah dikelola oleh migration utama.
    }
};
