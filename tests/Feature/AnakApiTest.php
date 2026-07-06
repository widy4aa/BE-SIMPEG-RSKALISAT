<?php

namespace Tests\Feature;

use App\Models\Anak;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AnakApiTest extends TestCase
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
            'role' => 'pegawai',
        ]);

        $this->pegawai = Pegawai::create([
            'user_id' => $this->user->id,
            'nik' => $nik,
            'nama' => 'Test Pegawai Anak',
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

    public function test_can_upload_akta_kelahiran_via_post()
    {
        $token = $this->getAuthToken();

        $file = UploadedFile::fake()->create('akta.pdf', 100, 'application/pdf');

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/keluarga/anak', [
            'nama_lengkap' => 'Anak Test',
            'jenis_kelamin' => 'L',
            'akta_kelahiran_file' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        
        $anakId = $response->json('data.id');

        $responseList = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/keluarga');

        $responseList->assertStatus(200);
        $anakList = $responseList->json('data.rincian.anak');
        
        $this->assertCount(1, $anakList);
        $this->assertEquals('Anak Test', $anakList[0]['nama_lengkap']);
        $this->assertNotNull($anakList[0]['link_akta_kelahiran']);
        $this->assertStringContainsString('dokumen/anak/akta_kelahiran_', $anakList[0]['link_akta_kelahiran']);
    }

    public function test_can_update_akta_kelahiran_via_post_instead_of_put()
    {
        $token = $this->getAuthToken();

        $anak = Anak::create([
            'pegawai_pribadi_id' => $this->pegawai->pribadi->id,
            'nama_lengkap' => 'Anak Lama',
            'jenis_kelamin' => 'L',
        ]);

        $file = UploadedFile::fake()->create('akta_update.jpg', 100, 'image/jpeg');

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/keluarga/anak/' . $anak->id, [
            'nama_lengkap' => 'Anak Baru',
            'akta_kelahiran_file' => $file,
        ]);

        $response->assertStatus(200);

        $responseList = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/keluarga');

        $anakList = $responseList->json('data.rincian.anak');
        
        $this->assertEquals('Anak Baru', $anakList[0]['nama_lengkap']);
        $this->assertNotNull($anakList[0]['link_akta_kelahiran']);
        $this->assertStringContainsString('.jpg', $anakList[0]['link_akta_kelahiran']);
    }
}
