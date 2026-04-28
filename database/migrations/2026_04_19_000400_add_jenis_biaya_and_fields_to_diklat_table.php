<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * jenis_biaya, jp, total_biaya, dan jenis_biaya_id sudah dimasukkan ke migration utama
     * 2026_04_07_000100_create_hris_tables. Migration ini dipertahankan namun idempotent.
     */
    public function up(): void
    {
        if (! Schema::hasTable('jenis_biaya')) {
            Schema::create('jenis_biaya', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->timestamps();
            });
        }

        Schema::table('diklat', function (Blueprint $table) {
            if (! Schema::hasColumn('diklat', 'jp')) {
                $table->unsignedInteger('jp')->nullable()->after('waktu');
            }
            if (! Schema::hasColumn('diklat', 'total_biaya')) {
                $table->decimal('total_biaya', 15, 2)->nullable()->after('jp');
            }
            if (! Schema::hasColumn('diklat', 'jenis_biaya_id')) {
                $table->foreignId('jenis_biaya_id')
                    ->nullable()
                    ->after('total_biaya')
                    ->constrained('jenis_biaya')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('diklat', function (Blueprint $table) {
            if (Schema::hasColumn('diklat', 'jenis_biaya_id')) {
                $table->dropConstrainedForeignId('jenis_biaya_id');
            }
            if (Schema::hasColumn('diklat', 'jp')) {
                $table->dropColumn('jp');
            }
            if (Schema::hasColumn('diklat', 'total_biaya')) {
                $table->dropColumn('total_biaya');
            }
        });

        Schema::dropIfExists('jenis_biaya');
    }
};
