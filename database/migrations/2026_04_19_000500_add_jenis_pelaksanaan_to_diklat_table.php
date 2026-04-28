<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * jenis_pelaksanaan sudah dimasukkan ke migration utama. Migration ini idempotent.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('diklat', 'jenis_pelaksanaan')) {
            Schema::table('diklat', function (Blueprint $table) {
                $table->string('jenis_pelaksanaan')->nullable()->after('jenis_biaya_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('diklat', 'jenis_pelaksanaan')) {
            Schema::table('diklat', function (Blueprint $table) {
                $table->dropColumn('jenis_pelaksanaan');
            });
        }
    }
};
