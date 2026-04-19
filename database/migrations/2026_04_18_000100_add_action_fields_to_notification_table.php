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
            $table->string('type')->default('info')->after('user_id');
            $table->string('action_code')->nullable()->after('type');
            $table->json('action_payload')->nullable()->after('action_code');
            $table->boolean('is_resolved')->default(false)->after('is_read');
            $table->string('unique_key')->nullable()->after('is_resolved');

            $table->index(['user_id', 'type'], 'notification_user_type_idx');
            $table->index(['user_id', 'is_read'], 'notification_user_is_read_idx');
            $table->index(['user_id', 'is_resolved'], 'notification_user_is_resolved_idx');
            $table->index(['user_id', 'unique_key'], 'notification_user_unique_key_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropIndex('notification_user_type_idx');
            $table->dropIndex('notification_user_is_read_idx');
            $table->dropIndex('notification_user_is_resolved_idx');
            $table->dropIndex('notification_user_unique_key_idx');

            $table->dropColumn([
                'type',
                'action_code',
                'action_payload',
                'is_resolved',
                'unique_key',
            ]);
        });
    }
};
