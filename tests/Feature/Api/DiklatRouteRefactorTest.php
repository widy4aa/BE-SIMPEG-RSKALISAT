<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\Diklat\DiklatIndexController;
use App\Http\Controllers\Api\Diklat\DiklatPegawaiController;
use App\Http\Controllers\Api\Diklat\HrdDiklatController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DiklatRouteRefactorTest extends TestCase
{
    public function test_diklat_index_routes_are_registered_to_index_controller(): void
    {
        $this->assertRouteAction('GET', 'api/diklat', DiklatIndexController::class.'@index');
        $this->assertRouteAction('GET', 'api/diklat/all', DiklatIndexController::class.'@all');
    }

    public function test_diklat_pegawai_routes_are_registered_to_pegawai_controller(): void
    {
        $this->assertRouteAction('POST', 'api/diklat', DiklatPegawaiController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/diklat/{id}', DiklatPegawaiController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/diklat/{id}', DiklatPegawaiController::class.'@destroy');
        $this->assertRouteAction('POST', 'api/diklat/{id}/upload-laporan', DiklatPegawaiController::class.'@uploadLaporan');
    }

    public function test_hrd_diklat_routes_are_registered_to_hrd_diklat_controller(): void
    {
        $this->assertRouteAction('POST', 'api/hrd/diklat', HrdDiklatController::class.'@storeMaster');
        $this->assertRouteAction('PUT', 'api/hrd/diklat/{id}', HrdDiklatController::class.'@updateMaster');
        $this->assertRouteAction('GET', 'api/hrd/diklat/{id}/peserta', HrdDiklatController::class.'@peserta');
        $this->assertRouteAction('POST', 'api/hrd/diklat/{id}/peserta', HrdDiklatController::class.'@syncPeserta');
        $this->assertRouteAction('GET', 'api/hrd/diklat/status/layak', HrdDiklatController::class.'@menungguKelayakan');
        $this->assertRouteAction('GET', 'api/hrd/diklat/status/validasi', HrdDiklatController::class.'@menungguValidasi');
        $this->assertRouteAction('PATCH', 'api/hrd/diklat/{id}/status/layak', HrdDiklatController::class.'@updateStatusKelayakan');
        $this->assertRouteAction('PATCH', 'api/hrd/diklat/{id}/status/validasi', HrdDiklatController::class.'@updateStatusValidasi');
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
