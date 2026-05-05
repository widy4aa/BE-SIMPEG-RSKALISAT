<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PegawaiFlowTest extends TestCase
{
    public function test_admin_can_create_user_and_user_can_update_profile(): void
    {
        // Setup: Admin logs in
        $adminLogin = $this->postJson('/api/login', [
            'nik' => '3174010101010099',
            'password' => 'password'
        ]);

        if ($adminLogin->status() !== 200) {
            dump($adminLogin->json());
        }

        $adminLogin->assertStatus(200);
        $adminToken = $adminLogin->json('data.token') ?? $adminLogin->json('token');
        if (!$adminToken) {
            $adminToken = $adminLogin->json('data.access_token');
        }

        // Admin creates a new user
        $newNik = substr(uniqid('U_'), 0, 16);
        $newPassword = 'secret_password';
        
        $createResponse = $this->postJson('/api/pegawai', [
            'nik' => $newNik,
            'nama' => 'Test Employee',
            'password' => $newPassword,
        ], [
            'Authorization' => "Bearer $adminToken"
        ]);
        if ($createResponse->status() !== 201) {
            dump($createResponse->json());
        }

        $createResponse->assertStatus(201);
        $this->assertEquals($newNik, $createResponse->json('data.nik'));

        // The newly created user logs in
        $userLogin = $this->postJson('/api/login', [
            'nik' => $newNik,
            'password' => $newPassword
        ]);

        $userLogin->assertStatus(200);
        $userToken = $userLogin->json('data.access_token');

        // User updates their profile
        $updateResponse = $this->patchJson('/api/profile', [
            'alamat' => 'Jl. Test No. 99'
        ], [
            'Authorization' => "Bearer $userToken"
        ]);

        $updateResponse->assertStatus(200);
        $this->assertTrue($updateResponse->json('success'));

        // Admin changes the new user's role to 'hrd'
        $pegawaiId = $createResponse->json('data.id');
        $changeRoleResponse = $this->patchJson("/api/pegawai/{$pegawaiId}/change-role", [
            'role' => 'hrd'
        ], [
            'Authorization' => "Bearer $adminToken"
        ]);

        $changeRoleResponse->assertStatus(200);
        $this->assertEquals('hrd', $changeRoleResponse->json('data.role'));

        // Admin attempts to change their own role (Should fail)
        $adminPegawaiQuery = \App\Models\Pegawai::where('nik', '3174010101010099')->first();
        if ($adminPegawaiQuery) {
            $adminPegawaiId = $adminPegawaiQuery->id;
            $changeSelfRoleResponse = $this->patchJson("/api/pegawai/{$adminPegawaiId}/change-role", [
                'role' => 'pegawai'
            ], [
                'Authorization' => "Bearer $adminToken"
            ]);
            
            $changeSelfRoleResponse->assertStatus(400);
            $this->assertStringContainsString('Tidak dapat mengubah role diri sendiri', $changeSelfRoleResponse->json('message'));
        }
    }
}
