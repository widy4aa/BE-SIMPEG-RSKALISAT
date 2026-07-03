<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function getWhatsappSetting(): JsonResponse
    {
        $setting = Setting::where('key', 'whatsapp_token')->first();
        $token = $setting?->value ?? '';

        $deviceInfo = null;

        if ($token !== '') {
            try {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->withHeaders([
                        'Authorization' => $token,
                    ])->post('https://api.fonnte.com/device');

                if ($response->successful()) {
                    $deviceInfo = $response->json();
                }
            } catch (\Exception $e) {
                // Ignore error if device check fails
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil pengaturan WhatsApp',
            'data' => [
                'whatsapp_token' => $token,
                'device' => $deviceInfo,
            ]
        ]);
    }

    public function updateWhatsappSetting(Request $request): JsonResponse
    {
        $request->validate([
            'whatsapp_token' => 'nullable|string'
        ]);

        $setting = Setting::updateOrCreate(
            ['key' => 'whatsapp_token'],
            ['value' => $request->whatsapp_token]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan WhatsApp berhasil diperbarui',
            'data' => [
                'whatsapp_token' => $setting->value,
            ]
        ]);
    }

    public function getWhatsappTemplates(): JsonResponse
    {
        $keys = [
            'wa_template_dokumen_klinis',
            'wa_template_diklat_h1',
            'wa_template_diklat_laporan',
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil template WhatsApp',
            'data' => [
                'wa_template_dokumen_klinis' => $settings['wa_template_dokumen_klinis'] ?? "Halo {nama},\n\nKami mengingatkan bahwa dokumen {jenis_dokumen} Anda dengan nomor {nomor} akan / telah kedaluwarsa pada {tanggal_kadaluarsa}.\n\nAnda dapat mengecek dokumen terkait pada tautan berikut: {link_dokumen}\n\nMohon segera memproses perpanjangan dokumen tersebut.",
                'wa_template_diklat_h1' => $settings['wa_template_diklat_h1'] ?? "🎓 Halo {nama},\n\nIni adalah pengingat bahwa diklat Anda:\n*{nama_diklat}*\nakan dimulai *besok* ({tanggal_mulai}) di _{tempat}_.\n\nHarap hadir tepat waktu. Semangat! 💪",
                'wa_template_diklat_laporan' => $settings['wa_template_diklat_laporan'] ?? "📋 Halo {nama},\n\nDiklat *{nama_diklat}* telah selesai kemarin ({tanggal_selesai}).\n\nSegera upload *{label_dokumen}* Anda melalui aplikasi SIMPEG agar dapat diproses oleh HRD. 🙏",
            ]
        ]);
    }

    public function updateWhatsappTemplates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'wa_template_dokumen_klinis' => 'nullable|string',
            'wa_template_diklat_h1' => 'nullable|string',
            'wa_template_diklat_laporan' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Template WhatsApp berhasil diperbarui',
        ]);
    }

    public function previewWhatsappTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string|in:wa_template_dokumen_klinis,wa_template_diklat_h1,wa_template_diklat_laporan',
            'teks_template' => 'required|string',
        ]);

        $key = $request->input('key');
        $teks = $request->input('teks_template');
        $preview = $teks;

        if ($key === 'wa_template_dokumen_klinis') {
            $preview = str_replace(
                ['{nama}', '{jenis_dokumen}', '{nomor}', '{tanggal_kadaluarsa}', '{link_dokumen}'],
                ['Dr. Budi Santoso', 'STR Tenaga Kesehatan', '1234567890', date('d M Y'), 'http://simpeg-rskalisat.com/storage/dokumen/dummy.pdf'],
                $teks
            );
        } elseif ($key === 'wa_template_diklat_h1') {
            $preview = str_replace(
                ['{nama}', '{nama_diklat}', '{tanggal_mulai}', '{tempat}'],
                ['Dr. Budi Santoso', 'Pelatihan Keselamatan Pasien', date('d M Y', strtotime('+1 day')), 'Aula RS Kalisat'],
                $teks
            );
        } elseif ($key === 'wa_template_diklat_laporan') {
            $preview = str_replace(
                ['{nama}', '{nama_diklat}', '{tanggal_selesai}', '{label_dokumen}'],
                ['Dr. Budi Santoso', 'Pelatihan Keselamatan Pasien', date('d M Y', strtotime('-1 day')), 'sertifikat'],
                $teks
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Preview template WhatsApp',
            'data' => [
                'preview' => $preview,
            ]
        ]);
    }
}
