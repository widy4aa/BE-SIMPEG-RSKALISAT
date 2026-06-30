<?php

use App\Models\User;
use App\Repositories\Diklat\PegawaiDiklatRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Services\Notification\NotificationActionSyncService;
use App\Services\Notification\WhatsappService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('notifications:sync-dashboard-actions {--batch=50 : Jumlah user per batch}', function (NotificationActionSyncService $syncService) {
    $batch = max((int) $this->option('batch'), 1);

    $query = User::query()
        ->select('id')
        ->where('role', 'pegawai')
        ->where('is_active', true)
        ->orderBy('id');

    $total = (clone $query)->count();

    if ($total === 0) {
        $this->info('Tidak ada user pegawai aktif untuk disinkronkan.');

        return;
    }

    $processed = 0;
    $failed = 0;

    $this->info("Mulai sinkronisasi dashboard actions untuk {$total} user (batch: {$batch}).");

    $query->chunkById($batch, function ($users) use ($syncService, &$processed, &$failed): void {
        foreach ($users as $user) {
            try {
                $syncService->syncDashboardActionsByUserId((int) $user->id);
            } catch (\Throwable $e) {
                $failed++;

                Log::error('Gagal sinkronisasi dashboard action notification.', [
                    'user_id' => (int) $user->id,
                    'message' => $e->getMessage(),
                ]);
            }

            $processed++;
        }
    });

    $this->info("Sinkronisasi selesai. Processed: {$processed}, Failed: {$failed}.");
})->purpose('Sync notifikasi aksi dashboard untuk seluruh user pegawai secara bertahap.');

Schedule::command('notifications:sync-dashboard-actions --batch=50')
    ->dailyAt('01:00')
    ->withoutOverlapping();

Artisan::command('notifications:diklat-reminder', function (
    PegawaiDiklatRepository $diklatRepo,
    NotificationRepository $notifRepo,
    WhatsappService $whatsapp,
) {
    $pesertaList = $diklatRepo->getPesertaDiklatBesok();

    if ($pesertaList->isEmpty()) {
        $this->info('Tidak ada diklat yang dimulai besok.');
        return;
    }

    $sent = 0;
    $failed = 0;

    foreach ($pesertaList as $row) {
        try {
            $namaDiklat  = (string) ($row->nama_kegiatan ?? 'Diklat');
            $tanggal     = (string) ($row->tanggal_mulai ?? '');
            $tempat      = (string) ($row->tempat ?? '-');
            $namaPegawai = (string) ($row->pegawai_nama ?? 'Pegawai');
            $userId      = (int) ($row->user_id ?? 0);
            $noTelp      = (string) ($row->no_telp ?? '');

            // In-app
            if ($userId > 0) {
                $notifRepo->createInfo(
                    $userId,
                    'Pengingat: Diklat Besok',
                    "Diklat '{$namaDiklat}' dimulai besok ({$tanggal}) di {$tempat}. Harap hadir tepat waktu."
                );
            }

            // WhatsApp
            if ($noTelp !== '') {
                $no = preg_replace('/\D/', '', $noTelp);
                if (str_starts_with($no, '0')) {
                    $no = '62' . substr($no, 1);
                } elseif (! str_starts_with($no, '62')) {
                    $no = '62' . $no;
                }
                $pesan = "🎓 Halo {$namaPegawai},\n\nIni adalah pengingat bahwa diklat Anda:\n*{$namaDiklat}*\nakan dimulai *besok* ({$tanggal}) di _{$tempat}_.\n\nHarap hadir tepat waktu. Semangat! 💪";
                $whatsapp->sendMessage($no, $pesan);
            }

            $sent++;
        } catch (\Throwable $e) {
            $failed++;
            Log::error('Gagal kirim reminder diklat H-1.', [
                'jadwal_id' => $row->id ?? null,
                'message'   => $e->getMessage(),
            ]);
        }
    }

    $this->info("Reminder diklat H-1 selesai. Terkirim: {$sent}, Gagal: {$failed}.");
})->purpose('Kirim notifikasi pengingat H-1 jadwal diklat ke pegawai peserta.');

Artisan::command('notifications:diklat-laporan-reminder', function (
    PegawaiDiklatRepository $diklatRepo,
    NotificationRepository $notifRepo,
    WhatsappService $whatsapp,
) {
    $pesertaList = $diklatRepo->getPesertaDiklatSelesaiH1BelumUploadLaporan();

    if ($pesertaList->isEmpty()) {
        $this->info('Tidak ada peserta diklat yang perlu diingatkan upload laporan.');
        return;
    }

    $sent = 0;
    $failed = 0;

    foreach ($pesertaList as $row) {
        try {
            $namaDiklat      = (string) ($row->nama_kegiatan ?? 'Diklat');
            $tanggalSelesai  = (string) ($row->tanggal_selesai ?? '');
            $jenis           = strtolower((string) ($row->jenis_pelaksanaan ?? ''));
            $labelDokumen    = $jenis === 'internal' ? 'laporan' : 'sertifikat';
            $namaPegawai     = (string) ($row->pegawai_nama ?? 'Pegawai');
            $userId          = (int) ($row->user_id ?? 0);
            $noTelp          = (string) ($row->no_telp ?? '');

            // In-app
            if ($userId > 0) {
                $notifRepo->createInfo(
                    $userId,
                    'Segera Upload ' . ucfirst($labelDokumen) . ' Diklat',
                    "Diklat '{$namaDiklat}' telah selesai ({$tanggalSelesai}). Segera upload {$labelDokumen} Anda melalui menu Diklat."
                );
            }

            // WhatsApp
            if ($noTelp !== '') {
                $no = preg_replace('/\D/', '', $noTelp);
                if (str_starts_with($no, '0')) {
                    $no = '62' . substr($no, 1);
                } elseif (! str_starts_with($no, '62')) {
                    $no = '62' . $no;
                }
                $pesan = "📋 Halo {$namaPegawai},\n\nDiklat *{$namaDiklat}* telah selesai kemarin ({$tanggalSelesai}).\n\nSegera upload *{$labelDokumen}* Anda melalui aplikasi SIMPEG agar dapat diproses oleh HRD. 🙏";
                $whatsapp->sendMessage($no, $pesan);
            }

            $sent++;
        } catch (\Throwable $e) {
            $failed++;
            Log::error('Gagal kirim reminder upload laporan diklat H+1.', [
                'jadwal_id' => $row->id ?? null,
                'message'   => $e->getMessage(),
            ]);
        }
    }

    $this->info("Reminder upload laporan diklat H+1 selesai. Terkirim: {$sent}, Gagal: {$failed}.");
})->purpose('Kirim notifikasi pengingat upload laporan/sertifikat diklat 1 hari setelah diklat selesai.');

Schedule::command('notifications:diklat-reminder')
    ->dailyAt('07:00')
    ->withoutOverlapping();

Schedule::command('notifications:diklat-laporan-reminder')
    ->dailyAt('07:05')
    ->withoutOverlapping();
