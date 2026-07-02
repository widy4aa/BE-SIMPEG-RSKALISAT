<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Notification\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    /**
     * Jeda waktu (detik) sebelum user boleh meminta / kirim ulang OTP.
     * Mencegah spam SMS/WhatsApp akibat klik "Kirim Ulang OTP" berulang.
     */
    private const RESEND_COOLDOWN_SECONDS = 60;

    public function requestOtp(Request $request, WhatsappService $whatsappService): JsonResponse
    {
        $request->validate([
            'nik' => 'required|string',
        ]);

        $key = 'request-otp:' . $request->nik;

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Harap tunggu {$seconds} detik sebelum meminta OTP lagi.",
                'cooldown_seconds' => $seconds,
            ], 429);
        }

        $pegawai = \App\Models\Pegawai::with('user', 'pribadi')->where('nik', $request->nik)->first();

        if (!$pegawai || !$pegawai->user) {
            return response()->json([
                'success' => false,
                'message' => 'User dengan NIK tersebut tidak ditemukan atau belum memiliki akun.',
            ], 404);
        }

        $user = $pegawai->user;
        $noTelp = $pegawai->pribadi?->no_telp;

        if (empty($noTelp)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor telepon belum terdaftar. Silakan hubungi admin.',
            ], 400);
        }

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP in cache for 5 minutes
        Cache::put('otp_reset_' . $request->nik, $otp, now()->addMinutes(5));

        // Format phone number to standard format if necessary.
        // Assuming Fonnte can handle 08xxx as well as 628xxx, we just send it.
        $message = "Halo {$pegawai->nama},\n\nKode OTP untuk reset password Anda adalah *{$otp}*.\n\nKode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapapun.";
        
        $whatsappService->sendMessage($noTelp, $message);

        \Illuminate\Support\Facades\RateLimiter::hit($key, self::RESEND_COOLDOWN_SECONDS);

        return response()->json([
            'success' => true,
            'message' => 'OTP berhasil dikirim ke nomor WhatsApp Anda.',
            'cooldown_seconds' => self::RESEND_COOLDOWN_SECONDS,
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'nik' => 'required|string',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $cachedOtp = Cache::get('otp_reset_' . $request->nik);

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.',
            ], 400);
        }

        $pegawai = \App\Models\Pegawai::where('nik', $request->nik)->first();
        $user = $pegawai ? $pegawai->user : null;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User dengan NIK tersebut tidak ditemukan.',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget('otp_reset_' . $request->nik);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah. Silakan login menggunakan password baru Anda.',
        ]);
    }
}
