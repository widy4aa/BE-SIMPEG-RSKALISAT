<?php

namespace Tests\Feature\Api;

use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\StrPegawai;
use App\Models\User;
use App\Services\Notification\NotificationActionSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationActionLifecycleTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_sync_resolves_old_action_and_creates_new_str_action_after_data_change(): void
    {
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
}
