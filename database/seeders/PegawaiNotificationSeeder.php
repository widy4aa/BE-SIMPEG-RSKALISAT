<?php

namespace Database\Seeders;

use App\Models\NotificationModel;
use App\Models\User;
use Illuminate\Database\Seeder;

class PegawaiNotificationSeeder extends Seeder
{
    /**
     * Seed notifikasi khusus untuk user role pegawai.
     */
    public function run(): void
    {
        $pegawaiUsers = User::query()->where('role', 'pegawai')->get();

        foreach ($pegawaiUsers as $user) {
            // Reset notifikasi agar hasil seeding konsisten.
            NotificationModel::query()->where('user_id', $user->id)->delete();

            $notifications = [
                [
                    'title' => 'Jadwal Diklat Mendatang',
                    'message' => 'Anda memiliki jadwal diklat yang belum terlaksana. Silakan cek detail jadwal.',
                    'is_read' => false,
                ],
                [
                    'title' => 'Kelengkapan STR',
                    'message' => 'Mohon pastikan masa berlaku STR Anda masih aktif.',
                    'is_read' => false,
                ],
                [
                    'title' => 'Profil Pegawai Diperbarui',
                    'message' => 'Perubahan data profil pegawai Anda telah disimpan.',
                    'is_read' => true,
                ],
            ];

            foreach ($notifications as $notification) {
                NotificationModel::query()->create([
                    'user_id' => $user->id,
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'is_read' => $notification['is_read'],
                ]);
            }
        }
    }
}
