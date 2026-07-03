<?php

namespace Tests\Feature\Api;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappTemplateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pegawai;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->pegawai = User::factory()->create(['role' => 'pegawai', 'is_active' => true]);
    }

    public function test_admin_can_get_whatsapp_templates(): void
    {
        Setting::create(['key' => 'wa_template_dokumen_klinis', 'value' => 'Test dok klinis']);

        $response = $this->withTokenFor($this->admin)->getJson('/api/settings/whatsapp-templates');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'wa_template_dokumen_klinis',
                         'wa_template_diklat_h1',
                         'wa_template_diklat_laporan',
                     ]
                 ]);

        $response->assertJsonPath('data.wa_template_dokumen_klinis', 'Test dok klinis');
        // Default fallbacks should be present for the others
        $this->assertNotEmpty($response->json('data.wa_template_diklat_h1'));
    }

    public function test_admin_can_update_whatsapp_templates(): void
    {
        $payload = [
            'wa_template_dokumen_klinis' => 'Template Baru Dokumen Klinis',
            'wa_template_diklat_h1' => 'Template Baru Diklat H-1',
            'wa_template_diklat_laporan' => 'Template Baru Laporan',
        ];

        $response = $this->withTokenFor($this->admin)->putJson('/api/settings/whatsapp-templates', $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Template WhatsApp berhasil diperbarui',
                 ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'wa_template_dokumen_klinis',
            'value' => 'Template Baru Dokumen Klinis',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'wa_template_diklat_h1',
            'value' => 'Template Baru Diklat H-1',
        ]);
    }

    public function test_admin_can_preview_whatsapp_templates(): void
    {
        $payload = [
            'key' => 'wa_template_dokumen_klinis',
            'teks_template' => 'Pesan untuk {nama} mengenai {jenis_dokumen} dengan nomor {nomor} yang kadaluarsa tanggal {tanggal_kadaluarsa}. Link: {link_dokumen}'
        ];

        $response = $this->withTokenFor($this->admin)->postJson('/api/settings/whatsapp-templates/preview', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'preview'
                     ]
                 ]);

        $previewText = $response->json('data.preview');
        $this->assertStringContainsString('Pesan untuk Dr. Budi Santoso mengenai STR Tenaga Kesehatan', $previewText);
        $this->assertStringContainsString('1234567890', $previewText);
    }

    public function test_non_admin_cannot_access_whatsapp_templates(): void
    {
        // Unauthenticated
        $this->getJson('/api/settings/whatsapp-templates')->assertStatus(401);
        
        // As pegawai
        $this->withTokenFor($this->pegawai)->getJson('/api/settings/whatsapp-templates')->assertStatus(403);
        $this->withTokenFor($this->pegawai)->putJson('/api/settings/whatsapp-templates', [])->assertStatus(403);
        $this->withTokenFor($this->pegawai)->postJson('/api/settings/whatsapp-templates/preview', [])->assertStatus(403);
    }

    private function withTokenFor(User $user): self
    {
        $token = app(\App\Services\Security\JwtService::class)->generate([
            'sub' => (string) $user->id,
            'role' => $user->role,
        ])['token'];

        return $this->withHeader('Authorization', 'Bearer '.$token);
    }
}
