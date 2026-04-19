<?php

namespace Database\Seeders;

use App\Models\NotificationModel;
use App\Models\PegawaiPribadi;
use App\Models\StrPegawai;
use App\Models\User;
use Illuminate\Database\Seeder;

class PegawaiActionNotificationSeeder extends Seeder
{
    /**
     * Seed notifikasi aksi untuk dashboard pegawai.
     */
    public function run(): void
    {
        $pegawaiUsers = User::query()->where('role', 'pegawai')->get();

        foreach ($pegawaiUsers as $user) {
            // Siapkan kondisi data agar rule dashboard action aktif saat endpoint dipanggil.
            $pegawaiId = (int) optional($user->pegawai)->id;

            if ($pegawaiId > 0) {
                StrPegawai::query()->where('pegawai_id', $pegawaiId)->delete();

                PegawaiPribadi::query()
                    ->where('pegawai_id', $pegawaiId)
                    ->update(['buku_nikah_file_path' => null]);
            }

            // Reset action notification agar seed idempoten.
            NotificationModel::query()
                ->where('user_id', $user->id)
                ->where('type', 'action')
                ->delete();

            $actionNotifications = [
                [
                    'type' => 'action',
                    'action_code' => 'str_missing',
                    'unique_key' => 'dashboard.str.missing',
                    'title' => 'STR Belum Tersedia',
                    'message' => 'Silakan lengkapi data STR agar proses verifikasi kepegawaian berjalan lancar.',
                    'action_payload' => [
                        'aksi' => 'lengkapi_str',
                        'label' => 'Lengkapi STR',
                        'target' => 'str',
                        'severity' => 'high',
                    ],
                    'is_read' => false,
                    'is_resolved' => false,
                ],
                [
                    'type' => 'action',
                    'action_code' => 'keluarga_incomplete',
                    'unique_key' => 'dashboard.keluarga.incomplete',
                    'title' => 'Data Keluarga Belum Lengkap',
                    'message' => 'Perbarui data keluarga Anda agar data administrasi tetap valid.',
                    'action_payload' => [
                        'aksi' => 'lengkapi_keluarga',
                        'label' => 'Lengkapi Data Keluarga',
                        'target' => 'keluarga',
                        'severity' => 'medium',
                    ],
                    'is_read' => false,
                    'is_resolved' => false,
                ],
                [
                    'type' => 'action',
                    'action_code' => 'str_will_expire',
                    'unique_key' => 'dashboard.str.will_expire',
                    'title' => 'Masa Berlaku STR Hampir Habis',
                    'message' => 'STR Anda mendekati masa kadaluarsa. Segera lakukan perpanjangan.',
                    'action_payload' => [
                        'aksi' => 'perpanjang_str',
                        'label' => 'Perpanjang STR',
                        'target' => 'str',
                        'severity' => 'medium',
                    ],
                    'is_read' => true,
                    'is_resolved' => true,
                ],
            ];

            foreach ($actionNotifications as $notification) {
                NotificationModel::query()->create([
                    'user_id' => $user->id,
                    'type' => $notification['type'],
                    'action_code' => $notification['action_code'],
                    'unique_key' => $notification['unique_key'],
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'action_payload' => $notification['action_payload'],
                    'is_read' => $notification['is_read'],
                    'is_resolved' => $notification['is_resolved'],
                ]);
            }
        }
    }
}
