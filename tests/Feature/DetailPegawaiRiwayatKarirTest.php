<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\JabatanPegawai;
use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DetailPegawaiRiwayatKarirTest extends TestCase
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

    public function test_riwayat_karir_schema_is_consistent_with_self()
    {
        $token = $this->getAuthToken();

        $jabatan = Jabatan::create([
            'nama' => 'Koordinator Administrasi SDM',
            'tmt_mulai' => '2026-07-10',
            'tmt_selesai' => '2027-03-10',
            'sk_file_path' => 'dokumen/jabatan/sk-jabatan-62.pdf',
        ]);

        JabatanPegawai::create([
            'pegawai_id' => $this->pegawai->id,
            'jabatan_id' => $jabatan->id,
            'is_current' => false,
            'started_at' => '2026-07-10',
            'ended_at' => '2027-03-10',
            'note' => '',
        ]);

        $pangkat = Pangkat::create([
            'nama' => 'Penata Muda Tingkat V',
            'pejabat_penetap' => 'Gubernur',
            'tmt_sk' => '2026-07-09',
            'sk_file_path' => 'dokumen/pangkat/sk-pangkat-62.pdf',
        ]);

        PangkatPegawai::create([
            'pegawai_id' => $this->pegawai->id,
            'pangkat_id' => $pangkat->id,
            'is_current' => false,
            'started_at' => '2026-07-10',
            'ended_at' => null,
            'note' => '',
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/pegawai/' . $this->pegawai->id . '/riwayat-karir');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'riwayat_karir' => [
                    'jabatan' => [
                        '*' => [
                            'id',
                            'unit_kerja_id',
                            'unit_kerja_nama',
                            'nama_jabatan',
                            'is_current',
                            'tmt_mulai',
                            'tmt_selesai',
                            'link_sk',
                            'note',
                        ]
                    ],
                    'pangkat' => [
                        '*' => [
                            'id',
                            'nama_pangkat',
                            'is_current',
                            'pejabat_penetap',
                            'tmt_sk',
                            'started_at',
                            'ended_at',
                            'link_sk',
                            'note',
                        ]
                    ]
                ]
            ]
        ]);
    }
}
