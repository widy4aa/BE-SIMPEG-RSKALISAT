<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\Diklat\Managed\DiklatController as ManagedDiklatController;
use App\Http\Controllers\Api\Diklat\Managed\DiklatIndexController as ManagedDiklatIndexController;
use App\Http\Controllers\Api\Diklat\Self\DiklatController as SelfDiklatController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DiklatRouteRefactorTest extends TestCase
{
    // ── Unified: GET /diklat → ManagedDiklatIndexController (semua role) ────────

    public function test_diklat_index_route_is_registered_to_managed_index_controller(): void
    {
        $this->assertRouteAction('GET', 'api/diklat', ManagedDiklatIndexController::class.'@index');
    }

    // ── Self: Pegawai mutate diklat ───────────────────────────────────────────

    public function test_self_diklat_mutation_routes_are_registered_to_self_diklat_controller(): void
    {
        $this->assertRouteAction('POST', 'api/diklat', SelfDiklatController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/diklat/{id}', SelfDiklatController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/diklat/{id}', SelfDiklatController::class.'@destroy');
        $this->assertRouteAction('POST', 'api/diklat/{id}/upload-laporan', SelfDiklatController::class.'@uploadLaporan');
    }

    // ── Managed: HRD manage master diklat ────────────────────────────────────

    public function test_managed_hrd_diklat_master_routes_are_registered_to_managed_diklat_controller(): void
    {
        $this->assertRouteAction('POST', 'api/hrd/diklat', ManagedDiklatController::class.'@storeMaster');
        $this->assertRouteAction('PUT', 'api/hrd/diklat/{id}', ManagedDiklatController::class.'@updateMaster');
    }

    public function test_managed_hrd_diklat_peserta_routes_are_registered_to_managed_diklat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/diklat/{id}/peserta', ManagedDiklatController::class.'@peserta');
        $this->assertRouteAction('POST', 'api/hrd/diklat/{id}/peserta', ManagedDiklatController::class.'@syncPeserta');
    }

    public function test_managed_hrd_diklat_status_routes_are_registered_to_managed_diklat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/hrd/diklat/status/layak', ManagedDiklatController::class.'@menungguKelayakan');
        $this->assertRouteAction('GET', 'api/hrd/diklat/status/validasi', ManagedDiklatController::class.'@menungguValidasi');
        $this->assertRouteAction('PATCH', 'api/hrd/diklat/{id}/status/layak', ManagedDiklatController::class.'@updateStatusKelayakan');
        $this->assertRouteAction('PATCH', 'api/hrd/diklat/{id}/status/validasi', ManagedDiklatController::class.'@updateStatusValidasi');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Assert route exists with the given action (single match assertion).
     */
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

    /**
     * Assert that at least one route with the given URI+method points to the expected controller action.
     * Used when the same URI is registered multiple times (e.g. GET /diklat for both Self and Managed).
     */
    private function assertRouteActionExistsForController(string $method, string $uri, string $expectedAction): void
    {
        foreach (Route::getRoutes() as $route) {
            if ($route->uri() === $uri
                && in_array($method, $route->methods(), true)
                && $route->getActionName() === $expectedAction
            ) {
                $this->assertTrue(true);
                return;
            }
        }

        $this->fail("Route {$method} {$uri} with action {$expectedAction} is not registered.");
    }
}
