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
                $jenisDokumen = 'STR';
                $nomorDokumen = $doc->nomor_str;
                $kadaluarsa = $doc->tanggal_kadaluarsa;
                $filePath = $doc->sk_file_path;
            } else {
                $doc = Sip::with('pegawai.pribadi')->where('id', $docId)->where('pegawai_id', $id)->firstOrFail();
                $jenisDokumen = 'SIP';
                $nomorDokumen = $doc->nomor_sip;
                $kadaluarsa = $doc->tanggal_kadaluarsa;
                $filePath = $doc->sk_file_path;
            }

            return $this->processWaReminder($doc, $jenisDokumen, $nomorDokumen, $kadaluarsa, $filePath);
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
                'Penugasan Klinis',
                $doc->nomor_surat,
                $doc->tgl_kadaluarsa,
                $doc->dokumen_file_path,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data dokumen tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    private function processWaReminder($doc, string $jenisDokumen, ?string $nomorDokumen, $kadaluarsa, ?string $filePath): JsonResponse
    {
        $noTelp = $doc->pegawai->pribadi?->no_telp ?? '';

        if ($noTelp === '') {
            return response()->json(['success' => false, 'message' => 'Pegawai belum memasukkan nomor HP/Telepon.'], 422);
        }

        $templateKey = 'wa_template_dokumen_klinis';
        $templateDefault = "Halo {nama},\n\nKami mengingatkan bahwa dokumen {jenis_dokumen} Anda dengan nomor {nomor} akan / telah kedaluwarsa pada {tanggal_kadaluarsa}.\n\nAnda dapat mengecek dokumen terkait pada tautan berikut: {link_dokumen}\n\nMohon segera memproses perpanjangan dokumen tersebut.";
        $template = \App\Models\Setting::where('key', $templateKey)->value('value') ?: $templateDefault;

        $linkDokumen = $filePath ? url('storage/' . $filePath) : '-';

        $pesan = str_replace(
            ['{nama}', '{jenis_dokumen}', '{nomor}', '{tanggal_kadaluarsa}', '{link_dokumen}'],
            [$doc->pegawai->nama, $jenisDokumen, $nomorDokumen ?? '-', $kadaluarsa->format('d M Y'), $linkDokumen],
            $template
        );

        $result = $this->whatsapp->sendMessage($this->formatPhoneNumber($noTelp), $pesan);

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim pesan: '.($result['message'] ?? 'Terjadi kesalahan.')], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Pesan pengingat {$jenisDokumen} berhasil dikirim.",
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
