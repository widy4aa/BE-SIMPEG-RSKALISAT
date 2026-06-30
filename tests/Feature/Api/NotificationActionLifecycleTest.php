<?php

namespace Tests\Feature\Api;

use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\PenugasanKlinis;
use App\Models\NotificationModel;
use App\Models\Sip;
use App\Models\StrPegawai;
use App\Models\User;
use App\Services\Notification\NotificationActionSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationActionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_sync_creates_action_notifications_for_incomplete_data(): void
    {
        [$user] = $this->createPegawaiWithIncompleteData();

        app(NotificationActionSyncService::class)->syncDashboardActionsByUserId((int) $user->id);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'str_missing',
            'is_resolved' => false,
            'unique_key' => 'dashboard.str.missing',
        ]);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'keluarga_incomplete',
            'is_resolved' => false,
            'unique_key' => 'dashboard.keluarga.incomplete',
        ]);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'profile_incomplete',
            'is_resolved' => false,
            'unique_key' => 'dashboard.profile.incomplete',
        ]);
    }

    public function test_sync_resolves_old_action_and_creates_new_str_action_after_data_change(): void
    {
        Carbon::setTestNow('2026-06-29 09:00:00');

        [$user, $pegawai] = $this->createPegawaiWithIncompleteData();

        $syncService = app(NotificationActionSyncService::class);
        $syncService->syncDashboardActionsByUserId((int) $user->id);

        StrPegawai::query()->updateOrCreate(
            ['pegawai_id' => $pegawai->id],
            [
                'nomor_str' => 'STR-TEST-LIFECYCLE',
                'tanggal_terbit' => now()->subYear()->toDateString(),
                'tanggal_kadaluarsa' => now()->addDays(60)->toDateString(),
                'sk_file_path' => 'dokumen/str/test-lifecycle.pdf',
            ]
        );

        $syncService->syncDashboardActionsByUserId((int) $user->id);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'str_missing',
            'is_resolved' => true,
            'unique_key' => 'dashboard.str.missing',
        ]);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'str_will_expire',
            'is_resolved' => false,
            'unique_key' => 'dashboard.str.will_expire',
        ]);

        $payload = $this->actionPayload((int) $user->id, 'dashboard.str.will_expire');

        $this->assertSame(60, $payload['sisa_hari']);
        $this->assertSame('60 hari sebelum', $payload['milestone_label']);
    }

    public function test_sync_creates_sip_will_expire_action_until_90_days_before_expiry(): void
    {
        Carbon::setTestNow('2026-06-29 09:00:00');

        [$user, $pegawai] = $this->createPegawaiWithIncompleteData();

        Sip::query()->create([
            'pegawai_id' => $pegawai->id,
            'nomor_sip' => 'SIP-TEST-90',
            'tanggal_terbit' => now()->subYear()->toDateString(),
            'tanggal_kadaluarsa' => now()->addDays(90)->toDateString(),
            'sk_file_path' => 'dokumen/sip/test-90.pdf',
        ]);

        app(NotificationActionSyncService::class)->syncDashboardActionsByUserId((int) $user->id);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'sip_will_expire',
            'is_resolved' => false,
            'unique_key' => 'dashboard.sip.will_expire',
        ]);

        $payload = $this->actionPayload((int) $user->id, 'dashboard.sip.will_expire');

        $this->assertSame(90, $payload['sisa_hari']);
        $this->assertSame('90 hari sebelum', $payload['milestone_label']);
    }

    public function test_sync_creates_penugasan_klinis_will_expire_action(): void
    {
        Carbon::setTestNow('2026-06-29 09:00:00');

        [$user, $pegawai] = $this->createPegawaiWithIncompleteData();

        PenugasanKlinis::query()->create([
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'PK-TEST-21',
            'tgl_mulai' => now()->subYear()->toDateString(),
            'tgl_kadaluarsa' => now()->addDays(21)->toDateString(),
            'dokumen_file_path' => 'dokumen/penugasan-klinis/test-21.pdf',
        ]);

        app(NotificationActionSyncService::class)->syncDashboardActionsByUserId((int) $user->id);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'penugasan_will_expire',
            'is_resolved' => false,
            'unique_key' => 'dashboard.penugasan.will_expire',
        ]);

        $payload = $this->actionPayload((int) $user->id, 'dashboard.penugasan.will_expire');

        $this->assertSame(21, $payload['sisa_hari']);
        $this->assertSame('21 hari sebelum', $payload['milestone_label']);
    }

    public function test_sync_creates_penugasan_klinis_expired_action_through_seven_days_after_expiry(): void
    {
        Carbon::setTestNow('2026-06-29 09:00:00');

        [$user, $pegawai] = $this->createPegawaiWithIncompleteData();

        PenugasanKlinis::query()->create([
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'PK-TEST-EXPIRED',
            'tgl_mulai' => now()->subYear()->toDateString(),
            'tgl_kadaluarsa' => now()->subDays(7)->toDateString(),
            'dokumen_file_path' => 'dokumen/penugasan-klinis/test-expired.pdf',
        ]);

        app(NotificationActionSyncService::class)->syncDashboardActionsByUserId((int) $user->id);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'penugasan_expired',
            'is_resolved' => false,
            'unique_key' => 'dashboard.penugasan.expired',
        ]);

        $payload = $this->actionPayload((int) $user->id, 'dashboard.penugasan.expired');

        $this->assertSame(-7, $payload['sisa_hari']);
        $this->assertSame('7 hari sesudah', $payload['milestone_label']);
    }

    public function test_sync_resolves_expiry_action_after_seven_days_after_expiry(): void
    {
        Carbon::setTestNow('2026-06-29 09:00:00');

        [$user, $pegawai] = $this->createPegawaiWithIncompleteData();

        PenugasanKlinis::query()->create([
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'PK-TEST-OLD-EXPIRED',
            'tgl_mulai' => now()->subYear()->toDateString(),
            'tgl_kadaluarsa' => now()->subDays(3)->toDateString(),
            'dokumen_file_path' => 'dokumen/penugasan-klinis/test-old-expired.pdf',
        ]);

        $syncService = app(NotificationActionSyncService::class);
        $syncService->syncDashboardActionsByUserId((int) $user->id);

        PenugasanKlinis::query()->where('pegawai_id', $pegawai->id)->update([
            'tgl_kadaluarsa' => now()->subDays(8)->toDateString(),
        ]);

        $syncService->syncDashboardActionsByUserId((int) $user->id);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'action',
            'action_code' => 'penugasan_expired',
            'is_resolved' => true,
            'unique_key' => 'dashboard.penugasan.expired',
        ]);
    }

    private function createPegawaiWithIncompleteData(): array
    {
        $user = User::query()->create([
            'username' => 'test_user_pegawai_lifecycle',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'is_active' => true,
        ]);

        $pegawai = Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => '9999000000000001',
            'nip' => '199001012020010001',
            'nama' => 'Pegawai Lifecycle Test',
            'status_pegawai' => 'aktif',
        ]);

        PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawai->id,
            'status_perkawinan' => 'kawin',
            'buku_nikah_file_path' => null,
        ]);

        return [$user, $pegawai];
    }

    private function actionPayload(int $userId, string $uniqueKey): array
    {
        return NotificationModel::query()
            ->where('user_id', $userId)
            ->where('unique_key', $uniqueKey)
            ->firstOrFail()
            ->action_payload;
    }
}
