<?php

namespace App\Repositories\Notification;

use App\Models\NotificationModel;
use Illuminate\Support\Collection;

class NotificationRepository
{
    public function getUnreadInfoByUserId(int $userId): Collection
    {
        return NotificationModel::query()
            ->where('user_id', $userId)
            ->where('type', 'info')
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getActiveActionsByUserId(int $userId): Collection
    {
        return NotificationModel::query()
            ->where('user_id', $userId)
            ->where('type', 'action')
            ->where('is_resolved', false)
            ->orderByDesc('created_at')
            ->get();
    }

    public function upsertAction(
        int $userId,
        string $uniqueKey,
        string $actionCode,
        string $title,
        string $message,
        array $payload,
    ): NotificationModel {
        return NotificationModel::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'unique_key' => $uniqueKey,
            ],
            [
                'type' => 'action',
                'action_code' => $actionCode,
                'title' => $title,
                'message' => $message,
                'action_payload' => $payload,
                'is_resolved' => false,
            ]
        );
    }

    public function resolveActionsNotIn(int $userId, array $activeUniqueKeys): int
    {
        $query = NotificationModel::query()
            ->where('user_id', $userId)
            ->where('type', 'action')
            ->where('is_resolved', false);

        if (! empty($activeUniqueKeys)) {
            $query->whereNotIn('unique_key', $activeUniqueKeys);
        }

        return $query->update([
            'is_resolved' => true,
        ]);
    }

    public function findByIdAndUserId(int $notificationId, int $userId): ?NotificationModel
    {
        return NotificationModel::query()
            ->where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
    }

    public function markAsRead(NotificationModel $notification): void
    {
        $notification->update([
            'is_read' => true,
        ]);
    }

    public function markAllAsRead(int $userId): int
    {
        return NotificationModel::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);
    }
}
