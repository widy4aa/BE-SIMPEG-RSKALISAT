<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\Hrd\HrdJabatanController;
use App\Http\Controllers\Api\Hrd\HrdPendidikanController;
use App\Http\Controllers\Api\Hrd\HrdReminderController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HrdRouteRefactorTest extends TestCase
{
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
