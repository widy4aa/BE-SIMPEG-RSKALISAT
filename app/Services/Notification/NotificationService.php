<?php

namespace App\Services\Notification;

use App\Repositories\Notification\NotificationRepository;

class NotificationService
{
    public function __construct(private readonly NotificationRepository $notificationRepository)
    {
    }

    public function markAsRead(int $userId, int $notificationId): array
    {
        $notification = $this->notificationRepository->findByIdAndUserId($notificationId, $userId);

        if ($notification === null) {
            return [
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
                'status' => 404,
            ];
        }

        if (! $notification->is_read) {
            $this->notificationRepository->markAsRead($notification);
        }

        return [
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
            'status' => 200,
        ];
    }

    public function markAllAsRead(int $userId): array
    {
        $updated = $this->notificationRepository->markAllAsRead($userId);

        return [
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
            'status' => 200,
            'data' => [
                'updated_count' => $updated,
            ],
        ];
    }
}
