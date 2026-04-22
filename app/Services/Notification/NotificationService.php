<?php

namespace App\Services\Notification;

use App\Repositories\Notification\NotificationRepository;

class NotificationService
{
    public function __construct(private readonly NotificationRepository $notificationRepository)
    {
    }

    public function listByUserId(int $userId): array
    {
        if ($userId <= 0) {
            return [
                'success' => false,
                'message' => 'User login tidak valid.',
                'status' => 401,
                'data' => null,
            ];
        }

        $notifications = $this->notificationRepository
            ->getUnreadInfoByUserId($userId)
            ->map(function ($notification) {
                return [
                    'id' => (int) $notification->id,
                    'title' => (string) ($notification->title ?? ''),
                    'message' => (string) ($notification->message ?? ''),
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => optional($notification->created_at)?->toDateTimeString(),
                ];
            })
            ->values()
            ->all();

        return [
            'success' => true,
            'message' => 'Daftar notifikasi berhasil diambil.',
            'status' => 200,
            'data' => [
                'notifications' => $notifications,
            ],
        ];
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
