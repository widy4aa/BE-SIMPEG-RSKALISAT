<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Models\PenugasanKlinis;
use App\Models\Sip;
use App\Models\StrPegawai;
use App\Services\Notification\WhatsappService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HrdReminderController extends Controller
{
    public function __construct(private readonly WhatsappService $whatsapp) {}

    public function sendReminderStrSip(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'tipe_dokumen' => 'required|in:str,sip',
                'dokumen_id' => 'required|integer',
            ]);

            $tipe = $request->input('tipe_dokumen');
            $docId = (int) $request->input('dokumen_id');

            if ($tipe === 'str') {
                $doc = StrPegawai::with('pegawai.pribadi')->where('id', $docId)->where('pegawai_id', $id)->firstOrFail();
                $namaDokumen = 'STR ('.$doc->nomor_str.')';
                $kadaluarsa = $doc->tanggal_kadaluarsa;
                $filePath = $doc->sk_file_path;
            } else {
                $doc = Sip::with('pegawai.pribadi')->where('id', $docId)->where('pegawai_id', $id)->firstOrFail();
                $namaDokumen = 'SIP ('.$doc->nomor_sip.')';
                $kadaluarsa = $doc->tanggal_kadaluarsa;
                $filePath = $doc->sk_file_path;
            }

            return $this->processWaReminder($doc, $namaDokumen, $kadaluarsa, $filePath);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data dokumen tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function sendReminderPenugasanKlinis(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'dokumen_id' => 'required|integer',
            ]);

            $docId = (int) $request->input('dokumen_id');
            $doc = PenugasanKlinis::with('pegawai.pribadi')->where('id', $docId)->where('pegawai_id', $id)->firstOrFail();

            return $this->processWaReminder(
                $doc,
                'Penugasan Klinis ('.$doc->nomor_surat.')',
                $doc->tgl_kadaluarsa,
                $doc->dokumen_file_path,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data dokumen tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    private function processWaReminder($doc, string $namaDokumen, $kadaluarsa, ?string $filePath): JsonResponse
    {
        $noTelp = $doc->pegawai->pribadi?->no_telp ?? '';

        if ($noTelp === '') {
            return response()->json(['success' => false, 'message' => 'Pegawai belum memasukkan nomor HP/Telepon.'], 422);
        }

        $selisihHari = (int) now()->diffInDays($kadaluarsa, false);

        if ($selisihHari < 0) {
            $urgensi = "🚨 *SANGAT MENDESAK* 🚨\nDokumen {$namaDokumen} Anda *TELAH KEDALUWARSA* sejak ".abs($selisihHari)." hari yang lalu ({$kadaluarsa->format('d-m-Y')}).";
        } elseif ($selisihHari <= 30) {
            $urgensi = "⚠️ *PENGINGAT PENTING* ⚠️\nDokumen {$namaDokumen} Anda akan segera kedaluwarsa dalam *{$selisihHari} hari* ({$kadaluarsa->format('d-m-Y')}).";
        } else {
            $urgensi = "ℹ️ *INFORMASI* ℹ️\nDokumen {$namaDokumen} Anda masih aktif hingga {$kadaluarsa->format('d-m-Y')}, namun kami mengingatkan Anda untuk mengecek kembali statusnya.";
        }

        $pesan = "Halo {$doc->pegawai->nama},\n\n{$urgensi}\n\nHarap segera berkoordinasi dengan pihak HRD untuk melakukan pembaruan dokumen demi kelancaran operasional RS.";

        if ($filePath) {
            $pesan .= "\n\nAnda dapat meninjau dokumen lama Anda pada tautan berikut:\n".asset($filePath);
        }

        $result = $this->whatsapp->sendMessage($this->formatPhoneNumber($noTelp), $pesan);

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim pesan: '.($result['message'] ?? 'Terjadi kesalahan.')], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Pesan pengingat {$namaDokumen} berhasil dikirim.",
        ]);
    }

    private function formatPhoneNumber(string $no): string
    {
        $no = preg_replace('/\D/', '', $no);
        if (str_starts_with($no, '0')) {
            return '62'.substr($no, 1);
        }

        if (str_starts_with($no, '62')) {
            return $no;
        }

        return '62'.$no;
    }
}
