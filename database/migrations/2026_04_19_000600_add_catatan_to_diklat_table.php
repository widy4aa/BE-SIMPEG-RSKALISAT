<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * catatan sudah dimasukkan ke migration utama. Migration ini idempotent.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('diklat', 'catatan')) {
            Schema::table('diklat', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('jenis_pelaksanaan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('diklat', 'catatan')) {
            Schema::table('diklat', function (Blueprint $table) {
                $table->dropColumn('catatan');
            });
        }
    }
};
