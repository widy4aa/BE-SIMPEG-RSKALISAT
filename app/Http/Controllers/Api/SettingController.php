<?php

namespace App\Http\Controllers\Api;

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
}
