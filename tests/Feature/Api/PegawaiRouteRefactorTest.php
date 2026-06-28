<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\Pegawai\Managed\PegawaiController as ManagedPegawaiController;
use App\Http\Controllers\Api\Pegawai\Managed\PegawaiListController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PegawaiRouteRefactorTest extends TestCase
{
    // ── Managed: List (index/show/store/changeRole) ───────────────────────────

    public function test_pegawai_list_routes_are_registered_to_managed_list_controller(): void
    {
        $this->assertRouteAction('GET', 'api/pegawai', PegawaiListController::class.'@index');
        $this->assertRouteAction('GET', 'api/pegawai/{id}', PegawaiListController::class.'@show');
        $this->assertRouteAction('GET', 'api/pegawai/{id}/{bagian}', PegawaiListController::class.'@showBagian');
        $this->assertRouteAction('POST', 'api/pegawai', PegawaiListController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/pegawai/{id}/change-role', PegawaiListController::class.'@changeRole');
    }

    // ── Managed: Update Inti & Pribadi ────────────────────────────────────────

    public function test_hrd_pegawai_inti_route_is_registered_to_managed_pegawai_controller(): void
    {
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/inti', ManagedPegawaiController::class.'@updateInti');
    }

    public function test_hrd_pegawai_pribadi_routes_are_registered_to_managed_pegawai_controller(): void
    {
        $this->assertRouteAction('PATCH', 'api/hrd/pegawai/{id}/pribadi', ManagedPegawaiController::class.'@updatePribadi');
        $this->assertRouteAction('POST', 'api/hrd/pegawai/{id}/pribadi', ManagedPegawaiController::class.'@updatePribadi');
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
