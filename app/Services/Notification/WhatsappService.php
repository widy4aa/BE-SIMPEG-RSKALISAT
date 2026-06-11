<?php

namespace App\Services\Notification;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Send a WhatsApp message using Fonnte API.
     *
     * @param string $target  The target phone number (e.g. '6281234567890')
     * @param string $message The message text to send
     * @return array
     */
    public function sendMessage(string $target, string $message): array
    {
        $setting = Setting::where('key', 'whatsapp_token')->first();
        $token = $setting?->value ?? '';

        if ($token === '') {
            Log::warning('WhatsApp API Token is not set in settings.');
            return [
                'success' => false,
                'message' => 'WhatsApp API token is not configured.',
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => $token,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Fonnte API Error: ' . $response->body());

            return [
                'success' => false,
                'message' => 'Failed to send message via Fonnte.',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'An error occurred while sending the message.',
                'error' => $e->getMessage(),
            ];
        }
    }
}
