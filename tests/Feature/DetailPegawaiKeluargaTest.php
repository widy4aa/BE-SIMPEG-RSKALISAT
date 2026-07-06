<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\User;
use App\Models\Anak;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DetailPegawaiKeluargaTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $pegawai;

    protected function setUp(): void
    {
        parent::setUp();
        
        $nik = (string) random_int(1000000000000000, 9999999999999999);
        $this->user = User::factory()->create([
            'username' => $nik,
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->pegawai = Pegawai::create([
            'user_id' => $this->user->id,
            'nik' => $nik,
            'nama' => 'Test Admin Pegawai',
        ]);
        
        PegawaiPribadi::create([
            'pegawai_id' => $this->pegawai->id,
            'jenis_kelamin' => 'L',
        ]);
    }

    private function getAuthToken(): string
    {
        $loginResponse = $this->postJson('/api/login', [
            'nik' => $this->user->username,
            'password' => 'password',
        ]);
        return $loginResponse->json('data.token') ?? $loginResponse->json('token') ?? $loginResponse->json('data.access_token');
    }

    public function test_keluarga_anak_schema_is_consistent_and_properly_casted()
    {
        $token = $this->getAuthToken();

        $anak = Anak::create([
            'pegawai_pribadi_id' => $this->pegawai->pribadi->id,
            'nama_lengkap' => 'Fajar Santoso',
            'jenis_kelamin' => 'L',
            'status_tanggungan' => true,
            'akta_kelahiran_file_path' => 'dokumen/anak/akta-123.pdf'
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/pegawai/' . $this->pegawai->id . '/keluarga');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'keluarga' => [
                    'total_keluarga',
                    'rincian' => [
                        'anak' => [
                            '*' => [
                                'id',
                                'pegawai_pribadi_id',
                                'nama_lengkap',
                                'jenis_kelamin',
                                'status_tanggungan',
                                'akta_kelahiran_file_path',
                                'created_at',
                                'updated_at',
                                'link_akta_kelahiran'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $anakData = $response->json('data.keluarga.rincian.anak.0');
        
        // Assert boolean cast
        $this->assertTrue($anakData['status_tanggungan'] === true, 'status_tanggungan should be boolean true, not integer 1');
        
        // Assert link formulation
        $this->assertEquals('/dokumen/anak/akta-123.pdf', $anakData['link_akta_kelahiran']);
        
        // Assert ISO-8601 formatting for dates from Eloquent
        $this->assertStringContainsString('T', $anakData['created_at'], 'created_at should be ISO-8601 formatted');
        $this->assertStringContainsString('Z', $anakData['created_at'], 'created_at should be ISO-8601 formatted');
    }
}
