<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jenis_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::table('diklat', function (Blueprint $table) {
            $table->unsignedInteger('jp')->nullable()->after('waktu');
            $table->decimal('total_biaya', 15, 2)->nullable()->after('jp');
            $table->foreignId('jenis_biaya_id')
                ->nullable()
                ->after('total_biaya')
                ->constrained('jenis_biaya')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diklat', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jenis_biaya_id');
            $table->dropColumn(['jp', 'total_biaya']);
        });

        Schema::dropIfExists('jenis_biaya');
    }
};
