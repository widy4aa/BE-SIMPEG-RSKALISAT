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
        Schema::table('diklat', function (Blueprint $table) {
            $table->enum('jenis_pelaksanaan', ['internal', 'external'])
                ->nullable()
                ->after('jenis_biaya_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diklat', function (Blueprint $table) {
            $table->dropColumn('jenis_pelaksanaan');
        });
    }
};
