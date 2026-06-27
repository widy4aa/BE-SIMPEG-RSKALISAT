<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\Keluarga\Self\AnakController as SelfAnakController;
use App\Http\Controllers\Api\Keluarga\Self\KontakDaruratController as SelfKontakDaruratController;
use App\Http\Controllers\Api\Keluarga\Self\OrangTuaController as SelfOrangTuaController;
use App\Http\Controllers\Api\Keluarga\Self\PasanganController as SelfPasanganController;
use App\Http\Controllers\Api\Keluarga\Self\TanggunganLainController as SelfTanggunganLainController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class KeluargaRouteRefactorTest extends TestCase
{
    // ── Self: Pasangan ────────────────────────────────────────────────────────

    public function test_self_pasangan_routes_are_registered_to_self_pasangan_controller(): void
    {
        $this->assertRouteAction('GET', 'api/keluarga/pasangan', SelfPasanganController::class.'@index');
        $this->assertRouteAction('POST', 'api/keluarga/pasangan', SelfPasanganController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/keluarga/pasangan/{id}', SelfPasanganController::class.'@update');
        $this->assertRouteAction('POST', 'api/keluarga/pasangan/{id}', SelfPasanganController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/keluarga/pasangan/{id}', SelfPasanganController::class.'@destroy');
    }

    // ── Self: Anak ────────────────────────────────────────────────────────────

    public function test_self_anak_routes_are_registered_to_self_anak_controller(): void
    {
        $this->assertRouteAction('GET', 'api/keluarga/anak', SelfAnakController::class.'@index');
        $this->assertRouteAction('POST', 'api/keluarga/anak', SelfAnakController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/keluarga/anak/{id}', SelfAnakController::class.'@update');
        $this->assertRouteAction('POST', 'api/keluarga/anak/{id}', SelfAnakController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/keluarga/anak/{id}', SelfAnakController::class.'@destroy');
    }

    // ── Self: OrangTua ────────────────────────────────────────────────────────

    public function test_self_orang_tua_routes_are_registered_to_self_orang_tua_controller(): void
    {
        $this->assertRouteAction('GET', 'api/keluarga/orang-tua', SelfOrangTuaController::class.'@index');
        $this->assertRouteAction('POST', 'api/keluarga/orang-tua', SelfOrangTuaController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/keluarga/orang-tua/{id}', SelfOrangTuaController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/keluarga/orang-tua/{id}', SelfOrangTuaController::class.'@destroy');
    }

    // ── Self: KontakDarurat ───────────────────────────────────────────────────

    public function test_self_kontak_darurat_routes_are_registered_to_self_kontak_darurat_controller(): void
    {
        $this->assertRouteAction('GET', 'api/keluarga/kontak-darurat', SelfKontakDaruratController::class.'@index');
        $this->assertRouteAction('POST', 'api/keluarga/kontak-darurat', SelfKontakDaruratController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/keluarga/kontak-darurat/{id}', SelfKontakDaruratController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/keluarga/kontak-darurat/{id}', SelfKontakDaruratController::class.'@destroy');
    }

    // ── Self: TanggunganLain ──────────────────────────────────────────────────

    public function test_self_tanggungan_lain_routes_are_registered_to_self_tanggungan_lain_controller(): void
    {
        $this->assertRouteAction('GET', 'api/keluarga/tanggungan-lain', SelfTanggunganLainController::class.'@index');
        $this->assertRouteAction('POST', 'api/keluarga/tanggungan-lain', SelfTanggunganLainController::class.'@store');
        $this->assertRouteAction('PATCH', 'api/keluarga/tanggungan-lain/{id}', SelfTanggunganLainController::class.'@update');
        $this->assertRouteAction('DELETE', 'api/keluarga/tanggungan-lain/{id}', SelfTanggunganLainController::class.'@destroy');
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
