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
                $templateKey = 'wa_template_diklat_h1';
                $templateDefault = "🎓 Halo {nama},\n\nIni adalah pengingat bahwa diklat Anda:\n*{nama_diklat}*\nakan dimulai *besok* ({tanggal_mulai}) di _{tempat}_.\n\nHarap hadir tepat waktu. Semangat! 💪";
                $template = \App\Models\Setting::where('key', $templateKey)->value('value') ?: $templateDefault;
                
                $pesan = str_replace(
                    ['{nama}', '{nama_diklat}', '{tanggal_mulai}', '{tempat}'],
                    [$namaPegawai, $namaDiklat, $tanggal, $tempat],
                    $template
                );
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
                $templateKey = 'wa_template_diklat_laporan';
                $templateDefault = "📋 Halo {nama},\n\nDiklat *{nama_diklat}* telah selesai kemarin ({tanggal_selesai}).\n\nSegera upload *{label_dokumen}* Anda melalui aplikasi SIMPEG agar dapat diproses oleh HRD. 🙏";
                $template = \App\Models\Setting::where('key', $templateKey)->value('value') ?: $templateDefault;
                
                $pesan = str_replace(
                    ['{nama}', '{nama_diklat}', '{tanggal_selesai}', '{label_dokumen}'],
                    [$namaPegawai, $namaDiklat, $tanggalSelesai, $labelDokumen],
                    $template
                );
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

Artisan::command('notifications:dokumen-klinis-reminder', function (
    App\Repositories\Notification\NotificationRepository $notifRepo,
    App\Services\Notification\WhatsappService $whatsapp,
) {
    $intervals = [
        'H-90' => \Carbon\Carbon::today()->addDays(90)->toDateString(),
        'H-60' => \Carbon\Carbon::today()->addDays(60)->toDateString(),
        'H-30' => \Carbon\Carbon::today()->addDays(30)->toDateString(),
        'H-7'  => \Carbon\Carbon::today()->addDays(7)->toDateString(),
        'H-1'  => \Carbon\Carbon::today()->addDays(1)->toDateString(),
        'H0'   => \Carbon\Carbon::today()->toDateString(),
        'H+7'  => \Carbon\Carbon::today()->subDays(7)->toDateString(),
    ];

    $templateKey = 'wa_template_dokumen_klinis';
    $templateDefault = "Halo {nama},\n\nKami mengingatkan bahwa dokumen {jenis_dokumen} Anda dengan nomor {nomor} akan / telah kedaluwarsa pada {tanggal_kadaluarsa}.\n\nAnda dapat mengecek dokumen terkait pada tautan berikut: {link_dokumen}\n\nMohon segera memproses perpanjangan dokumen tersebut.";
    $template = \App\Models\Setting::where('key', $templateKey)->value('value') ?: $templateDefault;

    $processDocuments = function ($query, $jenisDokumen, $colNomor, $colTanggal) use ($intervals, $template, $notifRepo, $whatsapp) {
        $sent = 0;
        $failed = 0;

        foreach ($intervals as $label => $date) {
            $docs = (clone $query)->where($colTanggal, $date)->with('pegawai.pribadi')->get();

            foreach ($docs as $doc) {
                if (!$doc->pegawai) continue;

                $nama = $doc->pegawai->nama;
                $nomor = $doc->{$colNomor} ?? '-';
                $tanggal = $doc->{$colTanggal}->format('d M Y');
                $userId = $doc->pegawai->user_id;
                $noTelp = $doc->pegawai->pribadi->no_telp ?? '';

                $fileCol = $jenisDokumen === 'Penugasan Klinis' ? 'dokumen_file_path' : 'sk_file_path';
                $linkDokumen = $doc->{$fileCol} ? url('storage/' . $doc->{$fileCol}) : '-';

                // In-app
                if ($userId) {
                    $notifRepo->createInfo(
                        $userId,
                        "Pengingat Kedaluwarsa $jenisDokumen ($label)",
                        "Dokumen $jenisDokumen Anda akan/telah kedaluwarsa pada $tanggal."
                    );
                }

                // WA
                if ($noTelp !== '') {
                    $no = preg_replace('/\D/', '', $noTelp);
                    if (str_starts_with($no, '0')) {
                        $no = '62' . substr($no, 1);
                    } elseif (!str_starts_with($no, '62')) {
                        $no = '62' . $no;
                    }

                    $pesan = str_replace(
                        ['{nama}', '{jenis_dokumen}', '{nomor}', '{tanggal_kadaluarsa}', '{link_dokumen}'],
                        [$nama, $jenisDokumen, $nomor, $tanggal, $linkDokumen],
                        $template
                    );

                    try {
                        $whatsapp->sendMessage($no, $pesan);
                        $sent++;
                    } catch (\Throwable $e) {
                        $failed++;
                        \Illuminate\Support\Facades\Log::error("Gagal kirim WA $jenisDokumen", ['id' => $doc->id, 'error' => $e->getMessage()]);
                    }
                }
            }
        }
        return [$sent, $failed];
    };

    $this->info("Memproses STR...");
    [$strSent, $strFailed] = $processDocuments(\App\Models\StrPegawai::query(), 'STR', 'nomor_str', 'tanggal_kadaluarsa');
    
    $this->info("Memproses SIP...");
    [$sipSent, $sipFailed] = $processDocuments(\App\Models\Sip::query(), 'SIP', 'nomor_sip', 'tanggal_kadaluarsa');
    
    $this->info("Memproses Penugasan Klinis...");
    [$pkSent, $pkFailed] = $processDocuments(\App\Models\PenugasanKlinis::query(), 'Penugasan Klinis', 'nomor_surat', 'tgl_kadaluarsa');

    $totalSent = $strSent + $sipSent + $pkSent;
    $totalFailed = $strFailed + $sipFailed + $pkFailed;

    $this->info("Reminder Dokumen Klinis Selesai. Terkirim: $totalSent, Gagal: $totalFailed.");
})->purpose('Kirim notifikasi pengingat dokumen klinis (STR, SIP, Penugasan Klinis) secara otomatis berdasarkan H-90, H-60, H-30, dll.');

Schedule::command('notifications:dokumen-klinis-reminder')
    ->dailyAt('08:00')
    ->withoutOverlapping();
