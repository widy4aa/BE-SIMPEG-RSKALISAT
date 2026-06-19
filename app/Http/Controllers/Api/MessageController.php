<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pesan\SendPesanPegawaiRequest;
use App\Models\Pegawai;
use App\Services\Notification\WhatsappService;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function __construct(private readonly WhatsappService $whatsapp) {}

    public function sendToPegawai(SendPesanPegawaiRequest $request, int $id): JsonResponse
    {
        $pegawai = Pegawai::with('pribadi')->find($id);

        if ($pegawai === null) {
            return response()->json(['success' => false, 'message' => 'Pegawai tidak ditemukan.'], 404);
        }

        $noTelp = $pegawai->pribadi?->no_telp ?? '';

        if ($noTelp === '') {
            return response()->json(['success' => false, 'message' => 'Pegawai belum memasukkan nomor HP/Telepon.'], 422);
        }

        $target = $this->formatPhoneNumber($noTelp);

        $result = $this->whatsapp->sendMessage($target, $request->validated()['pesan']);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim ke nomor ' . $this->maskPhoneNumber($noTelp),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $this->mapFonnteError($result),
        ], 422);
    }

    private function formatPhoneNumber(string $no): string
    {
        $no = preg_replace('/\D/', '', $no);

        if (str_starts_with($no, '0')) {
            return '62' . substr($no, 1);
        }

        if (str_starts_with($no, '62')) {
            return $no;
        }

        return '62' . $no;
    }

    private function maskPhoneNumber(string $no): string
    {
        $clean = preg_replace('/\D/', '', $no);
        if (strlen($clean) <= 4) {
            return $clean;
        }

        return substr($clean, 0, 4) . str_repeat('x', strlen($clean) - 4);
    }

    private function mapFonnteError(array $result): string
    {
        $reason = strtolower($result['error']['reason'] ?? $result['message'] ?? '');

        if (str_contains($reason, 'token') || str_contains($reason, 'invalid')) {
            return 'Gagal mengirim pesan: Token WhatsApp tidak valid atau belum disetting.';
        }

        if (str_contains($reason, 'quota') || str_contains($reason, 'limit')) {
            return 'Gagal mengirim pesan: Kuota WhatsApp (Fonnte) habis.';
        }

        if (str_contains($reason, 'not configured') || str_contains($reason, 'not set')) {
            return 'Gagal mengirim pesan: Token WhatsApp tidak valid atau belum disetting.';
        }

        return 'Gagal mengirim pesan: ' . ($result['error']['reason'] ?? 'Terjadi kesalahan pada layanan WhatsApp.');
    }
}
