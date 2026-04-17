<?php

namespace App\Repositories\Notification;

use App\Models\NotificationModel;

class NotificationRepository
{
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
