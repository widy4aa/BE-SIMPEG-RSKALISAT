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
        Schema::table('notification', function (Blueprint $table) {
            $table->dropIndex('notification_user_unique_key_idx');
            $table->unique(['user_id', 'unique_key'], 'notification_user_unique_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropUnique('notification_user_unique_key_unique');
            $table->index(['user_id', 'unique_key'], 'notification_user_unique_key_idx');
        });
    }
};
