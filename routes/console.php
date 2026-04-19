<?php

use App\Models\User;
use App\Services\Notification\NotificationActionSyncService;
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
