<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\Keluarga\Managed\AnakController as ManagedAnakController;
use App\Http\Controllers\Api\Keluarga\Managed\KontakDaruratController as ManagedKontakDaruratController;
use App\Http\Controllers\Api\Keluarga\Managed\OrangTuaController as ManagedOrangTuaController;
use App\Http\Controllers\Api\Keluarga\Managed\PasanganController as ManagedPasanganController;
use App\Http\Controllers\Api\Keluarga\Managed\TanggunganLainController as ManagedTanggunganLainController;
use App\Http\Controllers\Api\Pegawai\Managed\PegawaiController as ManagedPegawaiController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\JabatanController as ManagedJabatanController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PangkatController as ManagedPangkatController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PendidikanController as ManagedPendidikanController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PenugasanKlinisController as ManagedPenugasanKlinisController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\SipController as ManagedSipController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\StrController as ManagedStrController;
use App\Http\Controllers\Api\Hrd\HrdReminderController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HrdRouteRefactorTest extends TestCase
{
    // ── Keluarga Managed ──────────────────────────────────────────────────────

    public function test_hrd_pasangan_routes_are_registered_to_managed_pasangan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/pasangan', ManagedPasanganController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/pasangan', ManagedPasanganController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId}', ManagedPasanganController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId}', ManagedPasanganController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId}', ManagedPasanganController::class.'@destroy');
    }

    public function test_hrd_anak_routes_are_registered_to_managed_anak_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/anak', ManagedAnakController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/anak', ManagedAnakController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/anak/{keluargaId}', ManagedAnakController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/anak/{keluargaId}', ManagedAnakController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/anak/{keluargaId}', ManagedAnakController::class.'@destroy');
    }

    public function test_hrd_orang_tua_routes_are_registered_to_managed_orang_tua_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/orang-tua', ManagedOrangTuaController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/orang-tua', ManagedOrangTuaController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/orang-tua/{keluargaId}', ManagedOrangTuaController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/orang-tua/{keluargaId}', ManagedOrangTuaController::class.'@destroy');
    }

    public function test_hrd_kontak_darurat_routes_are_registered_to_managed_kontak_darurat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/kontak-darurat', ManagedKontakDaruratController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/kontak-darurat', ManagedKontakDaruratController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/kontak-darurat/{keluargaId}', ManagedKontakDaruratController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/kontak-darurat/{keluargaId}', ManagedKontakDaruratController::class.'@destroy');
    }

    public function test_hrd_tanggungan_lain_routes_are_registered_to_managed_tanggungan_lain_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/tanggungan-lain', ManagedTanggunganLainController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/tanggungan-lain', ManagedTanggunganLainController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/tanggungan-lain/{keluargaId}', ManagedTanggunganLainController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/tanggungan-lain/{keluargaId}', ManagedTanggunganLainController::class.'@destroy');
    }

    // ── RiwayatKarir Managed ─────────────────────────────────────────────────

    public function test_hrd_jabatan_routes_are_registered_to_managed_jabatan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan', ManagedJabatanController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan', ManagedJabatanController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}', ManagedJabatanController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}', ManagedJabatanController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}', ManagedJabatanController::class.'@destroy');
    }

    public function test_hrd_pendidikan_routes_are_registered_to_managed_pendidikan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan', ManagedPendidikanController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan', ManagedPendidikanController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}', ManagedPendidikanController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}', ManagedPendidikanController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}', ManagedPendidikanController::class.'@destroy');
    }

    public function test_hrd_pangkat_routes_are_registered_to_managed_pangkat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat', ManagedPangkatController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat', ManagedPangkatController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}', ManagedPangkatController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}', ManagedPangkatController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}', ManagedPangkatController::class.'@destroy');
    }

    public function test_hrd_str_routes_are_registered_to_managed_str_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/str', ManagedStrController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/str', ManagedStrController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}', ManagedStrController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}', ManagedStrController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}', ManagedStrController::class.'@destroy');
    }

    public function test_hrd_sip_routes_are_registered_to_managed_sip_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/sip', ManagedSipController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/sip', ManagedSipController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}', ManagedSipController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}', ManagedSipController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}', ManagedSipController::class.'@destroy');
    }

    public function test_hrd_penugasan_klinis_routes_are_registered_to_managed_penugasan_klinis_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis', ManagedPenugasanKlinisController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis', ManagedPenugasanKlinisController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}', ManagedPenugasanKlinisController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}', ManagedPenugasanKlinisController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}', ManagedPenugasanKlinisController::class.'@destroy');
    }

    // ── Pegawai Managed ───────────────────────────────────────────────────────

    public function test_hrd_pegawai_inti_pribadi_routes_are_registered_to_managed_pegawai_controller(): void
    {
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/inti', ManagedPegawaiController::class.'@updateInti');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/pribadi', ManagedPegawaiController::class.'@updatePribadi');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/pribadi', ManagedPegawaiController::class.'@updatePribadi');
    }

    // ── Reminder (tidak berubah namespace) ───────────────────────────────────

    public function test_hrd_reminder_routes_are_registered_to_reminder_controller(): void
    {
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/reminder/str-sip', HrdReminderController::class.'@sendReminderStrSip');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/reminder/penugasan-klinis', HrdReminderController::class.'@sendReminderPenugasanKlinis');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function assertRouteAction(string $method, string $uri, string $expectedAction): void
    {
        foreach (Route::getRoutes() as $route) {
            if ($route->uri() === $uri && in_array($method, $route->methods(), true)) {
                $this->assertSame($expectedAction, $route->getActionName());
                return;
            }
        }

        $this->fail("Route {$method} {$uri} is not registered.");
    }
}
