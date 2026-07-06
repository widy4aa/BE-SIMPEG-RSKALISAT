<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DiklatConsistencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_diklat_endpoint_response_structure_is_consistent_across_roles(): void
    {
        $roles = ['pegawai', 'hrd', 'direktur'];
        $responses = [];

        foreach ($roles as $role) {
            $user = $this->createUserWithRole($role);
            $token = $this->loginUser($user->pegawai->nik);

            $response = $this->getJson('/api/diklat?page=1&per_page=7', [
                'Authorization' => "Bearer $token"
            ]);

            $response->assertStatus(200);
            
            $json = $response->json();
            
            // Assert root structure
            $this->assertArrayHasKey('success', $json);
            $this->assertArrayHasKey('message', $json);
            $this->assertArrayHasKey('data', $json);
            
            // Assert data structure
            $this->assertArrayHasKey('role', $json['data']);
            $this->assertEquals($role, $json['data']['role']);
            $this->assertArrayHasKey('diklat', $json['data']);
            
            // Assert diklat summary structure (keys that were previously inconsistent)
            $diklat = $json['data']['diklat'];
            
            $this->assertArrayHasKey('ringkasan', $diklat);
            $this->assertArrayHasKey('total_riwayat', $diklat['ringkasan'], "Role $role is missing total_riwayat");
            $this->assertArrayHasKey('selesai', $diklat['ringkasan'], "Role $role is missing selesai");
            $this->assertArrayHasKey('akan_datang', $diklat['ringkasan'], "Role $role is missing akan_datang");
            
            $this->assertArrayHasKey('riwayat_diklat', $diklat, "Role $role is missing riwayat_diklat key for pagination");
            $this->assertArrayHasKey('data', $diklat['riwayat_diklat']); // the paginated items array
            
            $responses[$role] = $json;
        }

        // Additional assertion to ensure all ringkasan structures are identical
        $pegawaiRingkasanKeys = array_keys($responses['pegawai']['data']['diklat']['ringkasan']);
        $hrdRingkasanKeys = array_keys($responses['hrd']['data']['diklat']['ringkasan']);
        $direkturRingkasanKeys = array_keys($responses['direktur']['data']['diklat']['ringkasan']);

        sort($pegawaiRingkasanKeys);
        sort($hrdRingkasanKeys);
        sort($direkturRingkasanKeys);

        $this->assertEquals($pegawaiRingkasanKeys, $hrdRingkasanKeys, 'Pegawai and HRD ringkasan keys do not match');
        $this->assertEquals($pegawaiRingkasanKeys, $direkturRingkasanKeys, 'Pegawai and Direktur ringkasan keys do not match');
    }

    private function createUserWithRole(string $role): User
    {
        $uniqueId = str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $nik = '3174' . $uniqueId . '00';
        
        $user = User::query()->create([
            'username' => $nik,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);

        Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => $nik,
            'nama' => 'User ' . ucfirst($role),
            'status_pegawai' => 'aktif',
        ]);

        return clone $user->load('pegawai');
    }

    private function loginUser(string $nik): string
    {
        $loginResponse = $this->postJson('/api/login', [
            'nik' => $nik,
            'password' => 'password'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('data.token') ?? $loginResponse->json('token');
        if (!$token) {
            $token = $loginResponse->json('data.access_token');
        }

        return $token;
    }
}
