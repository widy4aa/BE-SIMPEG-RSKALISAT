<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\Hrd\HrdAnakController;
use App\Http\Controllers\Api\Hrd\HrdJabatanController;
use App\Http\Controllers\Api\Hrd\HrdKontakDaruratController;
use App\Http\Controllers\Api\Hrd\HrdOrangTuaController;
use App\Http\Controllers\Api\Hrd\HrdPangkatController;
use App\Http\Controllers\Api\Hrd\HrdPendidikanController;
use App\Http\Controllers\Api\Hrd\HrdPenugasanKlinisController;
use App\Http\Controllers\Api\Hrd\HrdPasanganController;
use App\Http\Controllers\Api\Hrd\HrdReminderController;
use App\Http\Controllers\Api\Hrd\HrdSipController;
use App\Http\Controllers\Api\Hrd\HrdStrController;
use App\Http\Controllers\Api\Hrd\HrdTanggunganLainController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HrdRouteRefactorTest extends TestCase
{
    public function test_hrd_pasangan_routes_are_registered_to_pasangan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/pasangan', HrdPasanganController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/pasangan', HrdPasanganController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId}', HrdPasanganController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId}', HrdPasanganController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId}', HrdPasanganController::class.'@destroy');
    }

    public function test_hrd_anak_routes_are_registered_to_anak_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/anak', HrdAnakController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/anak', HrdAnakController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/anak/{keluargaId}', HrdAnakController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/anak/{keluargaId}', HrdAnakController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/anak/{keluargaId}', HrdAnakController::class.'@destroy');
    }

    public function test_hrd_orang_tua_routes_are_registered_to_orang_tua_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/orang-tua', HrdOrangTuaController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/orang-tua', HrdOrangTuaController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/orang-tua/{keluargaId}', HrdOrangTuaController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/orang-tua/{keluargaId}', HrdOrangTuaController::class.'@destroy');
    }

    public function test_hrd_kontak_darurat_routes_are_registered_to_kontak_darurat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/kontak-darurat', HrdKontakDaruratController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/kontak-darurat', HrdKontakDaruratController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/kontak-darurat/{keluargaId}', HrdKontakDaruratController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/kontak-darurat/{keluargaId}', HrdKontakDaruratController::class.'@destroy');
    }

    public function test_hrd_tanggungan_lain_routes_are_registered_to_tanggungan_lain_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/keluarga/tanggungan-lain', HrdTanggunganLainController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/keluarga/tanggungan-lain', HrdTanggunganLainController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/keluarga/tanggungan-lain/{keluargaId}', HrdTanggunganLainController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/keluarga/tanggungan-lain/{keluargaId}', HrdTanggunganLainController::class.'@destroy');
    }

    public function test_hrd_jabatan_routes_are_registered_to_jabatan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan', HrdJabatanController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan', HrdJabatanController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}', HrdJabatanController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}', HrdJabatanController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId}', HrdJabatanController::class.'@destroy');
    }

    public function test_hrd_pendidikan_routes_are_registered_to_pendidikan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan', HrdPendidikanController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan', HrdPendidikanController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}', HrdPendidikanController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}', HrdPendidikanController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId}', HrdPendidikanController::class.'@destroy');
    }

    public function test_hrd_pangkat_routes_are_registered_to_pangkat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat', HrdPangkatController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat', HrdPangkatController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}', HrdPangkatController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}', HrdPangkatController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId}', HrdPangkatController::class.'@destroy');
    }

    public function test_hrd_str_routes_are_registered_to_str_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/str', HrdStrController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/str', HrdStrController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}', HrdStrController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}', HrdStrController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId}', HrdStrController::class.'@destroy');
    }

    public function test_hrd_sip_routes_are_registered_to_sip_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/sip', HrdSipController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/sip', HrdSipController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}', HrdSipController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}', HrdSipController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId}', HrdSipController::class.'@destroy');
    }

    public function test_hrd_penugasan_klinis_routes_are_registered_to_penugasan_klinis_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis', HrdPenugasanKlinisController::class.'@index');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis', HrdPenugasanKlinisController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}', HrdPenugasanKlinisController::class.'@update');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}', HrdPenugasanKlinisController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId}', HrdPenugasanKlinisController::class.'@destroy');
    }

    public function test_hrd_reminder_routes_are_registered_to_reminder_controller(): void
    {
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/reminder/str-sip', HrdReminderController::class.'@sendReminderStrSip');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/reminder/penugasan-klinis', HrdReminderController::class.'@sendReminderPenugasanKlinis');
    }

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
