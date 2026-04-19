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
        Schema::table('perubahan_data', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'user_id',
                'table_name',
                'record_id',
                'field_name',
                'old_value',
                'new_value',
            ]);

            $table->foreignId('by_user')
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('fitur')->after('by_user');
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('fitur');
            $table->text('note')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perubahan_data', function (Blueprint $table) {
            $table->dropForeign(['by_user']);

            $table->dropColumn([
                'by_user',
                'fitur',
                'status',
                'note',
            ]);

            $table->foreignId('user_id')
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('table_name')->nullable()->after('user_id');
            $table->unsignedBigInteger('record_id')->nullable()->after('table_name');
            $table->string('field_name')->nullable()->after('record_id');
            $table->text('old_value')->nullable()->after('field_name');
            $table->text('new_value')->nullable()->after('old_value');
        });
    }
};
