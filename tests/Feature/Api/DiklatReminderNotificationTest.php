<?php

namespace Tests\Feature\Api;

use App\Models\Diklat;
use App\Models\ListJadwalDiklat;
use App\Models\NotificationModel;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\User;
use App\Services\Notification\WhatsappService;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DiklatReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock WhatsApp agar tidak hit API eksternal
        $this->mock(WhatsappService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->andReturn(['success' => true]);
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    // ─────────────────────────────────────────────
    // H-1 Reminder Tests
    // ─────────────────────────────────────────────

    public function test_diklat_reminder_creates_inapp_notification_for_peserta_with_diklat_tomorrow(): void
    {
        Carbon::setTestNow('2026-07-01 07:00:00');

        [$user, $pegawai] = $this->createPegawai('9801000000000001', 'Peserta Diklat Besok', '081234000001');

        $diklat = $this->createDiklat('Pelatihan BHD', 'internal', Carbon::tomorrow(), Carbon::tomorrow()->addDay());
        ListJadwalDiklat::query()->create([
            'diklat_id'  => $diklat->id,
            'pegawai_id' => $pegawai->id,
        ]);

        $this->artisan('notifications:diklat-reminder')->assertSuccessful();

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type'    => 'info',
            'title'   => 'Pengingat: Diklat Besok',
        ]);
    }

    public function test_diklat_reminder_sends_wa_to_peserta_with_no_telp(): void
    {
        Carbon::setTestNow('2026-07-01 07:00:00');

        $whatsapp = $this->mock(WhatsappService::class);
        $whatsapp->shouldReceive('sendMessage')
            ->once()
            ->withArgs(fn ($no, $pesan) => str_starts_with($no, '62') && str_contains($pesan, 'Pelatihan BHD'))
            ->andReturn(['success' => true]);

        [$user, $pegawai] = $this->createPegawai('9801000000000002', 'Peserta WA Test', '081234000002');

        $diklat = $this->createDiklat('Pelatihan BHD', 'internal', Carbon::tomorrow(), Carbon::tomorrow()->addDay());
        ListJadwalDiklat::query()->create([
            'diklat_id'  => $diklat->id,
            'pegawai_id' => $pegawai->id,
        ]);

        $this->artisan('notifications:diklat-reminder')->assertSuccessful();
    }

    public function test_diklat_reminder_does_not_create_notification_for_diklat_not_tomorrow(): void
    {
        Carbon::setTestNow('2026-07-01 07:00:00');

        [$user, $pegawai] = $this->createPegawai('9801000000000003', 'Peserta Diklat Lusa', '081234000003');

        // Diklat lusa, bukan besok
        $diklat = $this->createDiklat('Diklat Lusa', 'internal', Carbon::now()->addDays(2), Carbon::now()->addDays(3));
        ListJadwalDiklat::query()->create([
            'diklat_id'  => $diklat->id,
            'pegawai_id' => $pegawai->id,
        ]);

        $this->artisan('notifications:diklat-reminder')->assertSuccessful();

        $this->assertDatabaseMissing('notification', [
            'user_id' => $user->id,
            'type'    => 'info',
            'title'   => 'Pengingat: Diklat Besok',
        ]);
    }

    public function test_diklat_reminder_notifies_multiple_peserta_for_same_diklat(): void
    {
        Carbon::setTestNow('2026-07-01 07:00:00');

        [$user1, $pegawai1] = $this->createPegawai('9801000000000004', 'Peserta Satu', '081234000004');
        [$user2, $pegawai2] = $this->createPegawai('9801000000000005', 'Peserta Dua', '081234000005');

        $diklat = $this->createDiklat('Diklat Multi Peserta', 'internal', Carbon::tomorrow(), Carbon::tomorrow()->addDay());
        ListJadwalDiklat::query()->create(['diklat_id' => $diklat->id, 'pegawai_id' => $pegawai1->id]);
        ListJadwalDiklat::query()->create(['diklat_id' => $diklat->id, 'pegawai_id' => $pegawai2->id]);

        $this->artisan('notifications:diklat-reminder')->assertSuccessful();

        $this->assertDatabaseHas('notification', ['user_id' => $user1->id, 'title' => 'Pengingat: Diklat Besok']);
        $this->assertDatabaseHas('notification', ['user_id' => $user2->id, 'title' => 'Pengingat: Diklat Besok']);
    }

    public function test_diklat_reminder_does_nothing_when_no_diklat_tomorrow(): void
    {
        Carbon::setTestNow('2026-07-01 07:00:00');

        $this->artisan('notifications:diklat-reminder')
            ->expectsOutput('Tidak ada diklat yang dimulai besok.')
            ->assertSuccessful();

        $this->assertDatabaseCount('notification', 0);
    }

    // ─────────────────────────────────────────────
    // H+1 Upload Laporan Reminder Tests
    // ─────────────────────────────────────────────

    public function test_laporan_reminder_creates_inapp_notification_for_internal_diklat_selesai_kemarin_belum_upload(): void
    {
        Carbon::setTestNow('2026-07-02 07:05:00');

        [$user, $pegawai] = $this->createPegawai('9802000000000001', 'Peserta Internal', '081235000001');

        $diklat = $this->createDiklat('Diklat Internal Selesai', 'internal', Carbon::yesterday()->subDay(), Carbon::yesterday());
        ListJadwalDiklat::query()->create([
            'diklat_id'        => $diklat->id,
            'pegawai_id'       => $pegawai->id,
            'sertif_file_path' => null,
        ]);

        $this->artisan('notifications:diklat-laporan-reminder')->assertSuccessful();

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type'    => 'info',
            'title'   => 'Segera Upload Laporan Diklat',
        ]);
    }

    public function test_laporan_reminder_creates_inapp_notification_for_external_diklat_selesai_kemarin_belum_upload(): void
    {
        Carbon::setTestNow('2026-07-02 07:05:00');

        [$user, $pegawai] = $this->createPegawai('9802000000000002', 'Peserta External', '081235000002');

        $diklat = $this->createDiklat('Diklat External Selesai', 'external', Carbon::yesterday()->subDay(), Carbon::yesterday());
        ListJadwalDiklat::query()->create([
            'diklat_id'        => $diklat->id,
            'pegawai_id'       => $pegawai->id,
            'sertif_file_path' => null,
        ]);

        $this->artisan('notifications:diklat-laporan-reminder')->assertSuccessful();

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type'    => 'info',
            'title'   => 'Segera Upload Sertifikat Diklat',
        ]);
    }

    public function test_laporan_reminder_sends_wa_with_correct_label_internal(): void
    {
        Carbon::setTestNow('2026-07-02 07:05:00');

        $whatsapp = $this->mock(WhatsappService::class);
        $whatsapp->shouldReceive('sendMessage')
            ->once()
            ->withArgs(fn ($no, $pesan) => str_contains($pesan, 'laporan'))
            ->andReturn(['success' => true]);

        [$user, $pegawai] = $this->createPegawai('9802000000000003', 'Peserta WA Internal', '081235000003');

        $diklat = $this->createDiklat('Diklat WA Internal', 'internal', Carbon::yesterday()->subDay(), Carbon::yesterday());
        ListJadwalDiklat::query()->create([
            'diklat_id'        => $diklat->id,
            'pegawai_id'       => $pegawai->id,
            'sertif_file_path' => null,
        ]);

        $this->artisan('notifications:diklat-laporan-reminder')->assertSuccessful();
    }

    public function test_laporan_reminder_sends_wa_with_correct_label_external(): void
    {
        Carbon::setTestNow('2026-07-02 07:05:00');

        $whatsapp = $this->mock(WhatsappService::class);
        $whatsapp->shouldReceive('sendMessage')
            ->once()
            ->withArgs(fn ($no, $pesan) => str_contains($pesan, 'sertifikat'))
            ->andReturn(['success' => true]);

        [$user, $pegawai] = $this->createPegawai('9802000000000004', 'Peserta WA External', '081235000004');

        $diklat = $this->createDiklat('Diklat WA External', 'external', Carbon::yesterday()->subDay(), Carbon::yesterday());
        ListJadwalDiklat::query()->create([
            'diklat_id'        => $diklat->id,
            'pegawai_id'       => $pegawai->id,
            'sertif_file_path' => null,
        ]);

        $this->artisan('notifications:diklat-laporan-reminder')->assertSuccessful();
    }

    public function test_laporan_reminder_skips_peserta_yang_sudah_upload(): void
    {
        Carbon::setTestNow('2026-07-02 07:05:00');

        [$user, $pegawai] = $this->createPegawai('9802000000000005', 'Peserta Sudah Upload', '081235000005');

        $diklat = $this->createDiklat('Diklat Sudah Upload', 'internal', Carbon::yesterday()->subDay(), Carbon::yesterday());
        ListJadwalDiklat::query()->create([
            'diklat_id'        => $diklat->id,
            'pegawai_id'       => $pegawai->id,
            'sertif_file_path' => 'dokumen/sertif/laporan-test.pdf',
        ]);

        $this->artisan('notifications:diklat-laporan-reminder')->assertSuccessful();

        $this->assertDatabaseMissing('notification', [
            'user_id' => $user->id,
            'title'   => 'Segera Upload Laporan Diklat',
        ]);
    }

    public function test_laporan_reminder_skips_diklat_yang_selesai_bukan_kemarin(): void
    {
        Carbon::setTestNow('2026-07-02 07:05:00');

        [$user, $pegawai] = $this->createPegawai('9802000000000006', 'Peserta Diklat Lama', '081235000006');

        // Selesai 3 hari lalu, bukan kemarin
        $diklat = $this->createDiklat('Diklat Lama', 'internal', Carbon::now()->subDays(4), Carbon::now()->subDays(3));
        ListJadwalDiklat::query()->create([
            'diklat_id'        => $diklat->id,
            'pegawai_id'       => $pegawai->id,
            'sertif_file_path' => null,
        ]);

        $this->artisan('notifications:diklat-laporan-reminder')->assertSuccessful();

        $this->assertDatabaseMissing('notification', [
            'user_id' => $user->id,
            'title'   => 'Segera Upload Laporan Diklat',
        ]);
    }

    public function test_laporan_reminder_does_nothing_when_no_diklat_selesai_kemarin(): void
    {
        Carbon::setTestNow('2026-07-02 07:05:00');

        $this->artisan('notifications:diklat-laporan-reminder')
            ->expectsOutput('Tidak ada peserta diklat yang perlu diingatkan upload laporan.')
            ->assertSuccessful();

        $this->assertDatabaseCount('notification', 0);
    }

    public function test_hrd_can_send_manual_upload_laporan_reminder_by_diklat_id_and_pegawai_id(): void
    {
        Carbon::setTestNow('2026-07-02 10:00:00');

        $hrd = $this->createHrdUser();
        [$user, $pegawai] = $this->createPegawai('9803000000000001', 'Peserta Manual Reminder', '081236000001');
        $diklat = $this->createDiklat('Diklat Manual Reminder', 'internal', Carbon::now()->subDays(2), Carbon::yesterday());

        ListJadwalDiklat::query()->create([
            'diklat_id' => $diklat->id,
            'pegawai_id' => $pegawai->id,
            'sertif_file_path' => null,
        ]);

        $response = $this->withTokenFor($hrd)
            ->postJson("/api/hrd/diklat/{$diklat->id}/pegawai/{$pegawai->id}/reminder-upload-laporan");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.diklat_id', $diklat->id)
            ->assertJsonPath('data.pegawai_id', $pegawai->id)
            ->assertJsonPath('data.label_dokumen', 'laporan')
            ->assertJsonPath('data.in_app_sent', true)
            ->assertJsonPath('data.whatsapp_sent', true);

        $this->assertDatabaseHas('notification', [
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Segera Upload Laporan Diklat',
        ]);
    }

    public function test_hrd_manual_upload_reminder_returns_422_when_participant_already_uploaded(): void
    {
        Carbon::setTestNow('2026-07-02 10:00:00');

        $hrd = $this->createHrdUser();
        [$user, $pegawai] = $this->createPegawai('9803000000000002', 'Peserta Sudah Upload Manual', '081236000002');
        $diklat = $this->createDiklat('Diklat Manual Sudah Upload', 'external', Carbon::now()->subDays(2), Carbon::yesterday());

        ListJadwalDiklat::query()->create([
            'diklat_id' => $diklat->id,
            'pegawai_id' => $pegawai->id,
            'sertif_file_path' => 'dokumen/sertif/manual-existing.pdf',
        ]);

        $response = $this->withTokenFor($hrd)
            ->postJson("/api/hrd/diklat/{$diklat->id}/pegawai/{$pegawai->id}/reminder-upload-laporan");

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Pegawai sudah upload sertifikat diklat.');

        $this->assertDatabaseMissing('notification', [
            'user_id' => $user->id,
            'title' => 'Segera Upload Sertifikat Diklat',
        ]);
    }

    public function test_pegawai_cannot_send_manual_upload_laporan_reminder(): void
    {
        $nonHrd = $this->createUser('pegawai', '9803000000000003');

        $this->withTokenFor($nonHrd)
            ->postJson('/api/hrd/diklat/1/pegawai/1/reminder-upload-laporan')
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    private function withTokenFor(User $user): self
    {
        $token = app(JwtService::class)->generate([
            'sub' => (string) $user->id,
            'role' => $user->role,
        ])['token'];

        return $this->withHeader('Authorization', 'Bearer '.$token);
    }

    private function createHrdUser(): User
    {
        return $this->createUser('hrd', '9803999999999999');
    }

    private function createUser(string $role, string $username): User
    {
        return User::query()->create([
            'username' => $username,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function createPegawai(string $nik, string $nama, string $noTelp): array
    {
        $user = User::query()->create([
            'username'  => $nik,
            'password'  => Hash::make('password'),
            'role'      => 'pegawai',
            'is_active' => true,
        ]);

        $pegawai = Pegawai::query()->create([
            'user_id'        => $user->id,
            'nik'            => $nik,
            'nama'           => $nama,
            'status_pegawai' => 'aktif',
        ]);

        PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawai->id,
            'no_telp'    => $noTelp,
        ]);

        return [$user, $pegawai];
    }

    private function createDiklat(string $nama, string $jenis, Carbon $mulai, Carbon $selesai): Diklat
    {
        return Diklat::query()->create([
            'nama_kegiatan'    => $nama,
            'jenis_pelaksanaan' => $jenis,
            'tanggal_mulai'    => $mulai->toDateString(),
            'tanggal_selesai'  => $selesai->toDateString(),
            'penyelenggara'    => 'RS Kalisat',
            'tempat'           => 'Ruang Pelatihan',
        ]);
    }
}
