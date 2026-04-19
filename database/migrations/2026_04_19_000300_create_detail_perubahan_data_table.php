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
        Schema::create('detail_perubahan_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perubahan_data')
                ->constrained('perubahan_data')
                ->cascadeOnDelete();
            $table->string('target_table');
            $table->string('kolom');
            $table->text('value')->nullable();
            $table->text('old_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_perubahan_data');
    }
};
