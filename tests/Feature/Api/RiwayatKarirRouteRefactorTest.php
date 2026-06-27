<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\RiwayatKarir\Managed\JabatanController as ManagedJabatanController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PangkatController as ManagedPangkatController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PendidikanController as ManagedPendidikanController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\PenugasanKlinisController as ManagedPenugasanKlinisController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\SipController as ManagedSipController;
use App\Http\Controllers\Api\RiwayatKarir\Managed\StrController as ManagedStrController;
use App\Http\Controllers\Api\RiwayatKarir\Self\JabatanController as SelfJabatanController;
use App\Http\Controllers\Api\RiwayatKarir\Self\PangkatController as SelfPangkatController;
use App\Http\Controllers\Api\RiwayatKarir\Self\PendidikanController as SelfPendidikanController;
use App\Http\Controllers\Api\RiwayatKarir\Self\PenugasanKlinisController as SelfPenugasanKlinisController;
use App\Http\Controllers\Api\RiwayatKarir\Self\SipController as SelfSipController;
use App\Http\Controllers\Api\RiwayatKarir\Self\StrController as SelfStrController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RiwayatKarirRouteRefactorTest extends TestCase
{
    // ── Self: Jabatan ─────────────────────────────────────────────────────────

    public function test_self_jabatan_routes_are_registered_to_self_jabatan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/riwayat-karir/jabatan', SelfJabatanController::class.'@index');
        $this->assertRouteAction('POST', 'api/riwayat-karir/jabatan', SelfJabatanController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/riwayat-karir/jabatan/{id}', SelfJabatanController::class.'@update');
        $this->assertRouteAction('POST', 'api/riwayat-karir/jabatan/{id}', SelfJabatanController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/riwayat-karir/jabatan/{id}', SelfJabatanController::class.'@destroy');
    }

    // ── Self: Pendidikan ──────────────────────────────────────────────────────

    public function test_self_pendidikan_routes_are_registered_to_self_pendidikan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/riwayat-karir/pendidikan', SelfPendidikanController::class.'@index');
        $this->assertRouteAction('POST', 'api/riwayat-karir/pendidikan', SelfPendidikanController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/riwayat-karir/pendidikan/{id}', SelfPendidikanController::class.'@update');
        $this->assertRouteAction('POST', 'api/riwayat-karir/pendidikan/{id}', SelfPendidikanController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/riwayat-karir/pendidikan/{id}', SelfPendidikanController::class.'@destroy');
    }

    // ── Self: Pangkat ─────────────────────────────────────────────────────────

    public function test_self_pangkat_routes_are_registered_to_self_pangkat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/riwayat-karir/pangkat', SelfPangkatController::class.'@index');
        $this->assertRouteAction('POST', 'api/riwayat-karir/pangkat', SelfPangkatController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/riwayat-karir/pangkat/{id}', SelfPangkatController::class.'@update');
        $this->assertRouteAction('POST', 'api/riwayat-karir/pangkat/{id}', SelfPangkatController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/riwayat-karir/pangkat/{id}', SelfPangkatController::class.'@destroy');
    }

    // ── Self: SIP ─────────────────────────────────────────────────────────────

    public function test_self_sip_routes_are_registered_to_self_sip_controller(): void
    {
        $this->assertRouteAction('GET', 'api/riwayat-karir/sip', SelfSipController::class.'@index');
        $this->assertRouteAction('POST', 'api/riwayat-karir/sip', SelfSipController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/riwayat-karir/sip/{id}', SelfSipController::class.'@update');
        $this->assertRouteAction('POST', 'api/riwayat-karir/sip/{id}', SelfSipController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/riwayat-karir/sip/{id}', SelfSipController::class.'@destroy');
    }

    // ── Self: STR ─────────────────────────────────────────────────────────────

    public function test_self_str_routes_are_registered_to_self_str_controller(): void
    {
        $this->assertRouteAction('GET', 'api/riwayat-karir/str', SelfStrController::class.'@index');
        $this->assertRouteAction('POST', 'api/riwayat-karir/str', SelfStrController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/riwayat-karir/str/{id}', SelfStrController::class.'@update');
        $this->assertRouteAction('POST', 'api/riwayat-karir/str/{id}', SelfStrController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/riwayat-karir/str/{id}', SelfStrController::class.'@destroy');
    }

    // ── Self: PenugasanKlinis ─────────────────────────────────────────────────

    public function test_self_penugasan_klinis_routes_are_registered_to_self_penugasan_klinis_controller(): void
    {
        $this->assertRouteAction('GET', 'api/riwayat-karir/penugasan-klinis', SelfPenugasanKlinisController::class.'@index');
        $this->assertRouteAction('POST', 'api/riwayat-karir/penugasan-klinis', SelfPenugasanKlinisController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/riwayat-karir/penugasan-klinis/{id}', SelfPenugasanKlinisController::class.'@update');
        $this->assertRouteAction('POST', 'api/riwayat-karir/penugasan-klinis/{id}', SelfPenugasanKlinisController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/riwayat-karir/penugasan-klinis/{id}', SelfPenugasanKlinisController::class.'@destroy');
    }

    // ── Managed: Jabatan ──────────────────────────────────────────────────────

    public function test_managed_jabatan_routes_are_registered_to_managed_jabatan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan', ManagedJabatanController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan', ManagedJabatanController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}', ManagedJabatanController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}', ManagedJabatanController::class.'@destroy');
    }

    // ── Managed: Pendidikan ───────────────────────────────────────────────────

    public function test_managed_pendidikan_routes_are_registered_to_managed_pendidikan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan', ManagedPendidikanController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan', ManagedPendidikanController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}', ManagedPendidikanController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}', ManagedPendidikanController::class.'@destroy');
    }

    // ── Managed: Pangkat ──────────────────────────────────────────────────────

    public function test_managed_pangkat_routes_are_registered_to_managed_pangkat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat', ManagedPangkatController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat', ManagedPangkatController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}', ManagedPangkatController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}', ManagedPangkatController::class.'@destroy');
    }

    // ── Managed: SIP ──────────────────────────────────────────────────────────

    public function test_managed_sip_routes_are_registered_to_managed_sip_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/sip', ManagedSipController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/sip', ManagedSipController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}', ManagedSipController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}', ManagedSipController::class.'@destroy');
    }

    // ── Managed: STR ──────────────────────────────────────────────────────────

    public function test_managed_str_routes_are_registered_to_managed_str_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/str', ManagedStrController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/str', ManagedStrController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}', ManagedStrController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}', ManagedStrController::class.'@destroy');
    }

    // ── Managed: PenugasanKlinis ──────────────────────────────────────────────

    public function test_managed_penugasan_klinis_routes_are_registered_to_managed_penugasan_klinis_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis', ManagedPenugasanKlinisController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis', ManagedPenugasanKlinisController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}', ManagedPenugasanKlinisController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}', ManagedPenugasanKlinisController::class.'@destroy');
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
