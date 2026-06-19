<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StoreJabatanRequest;
use App\Http\Requests\RiwayatKarir\StorePangkatRequest;
use App\Http\Requests\RiwayatKarir\StorePenugasanKlinisRequest;
use App\Http\Requests\RiwayatKarir\StoreSipRequest;
use App\Http\Requests\RiwayatKarir\StoreStrRequest;
use App\Http\Requests\RiwayatKarir\UpdateJabatanRequest;
use App\Http\Requests\RiwayatKarir\UpdatePangkatRequest;
use App\Http\Requests\RiwayatKarir\UpdatePenugasanKlinisRequest;
use App\Http\Requests\RiwayatKarir\UpdateSipRequest;
use App\Http\Requests\RiwayatKarir\UpdateStrRequest;
use App\Models\PenugasanKlinis;
use App\Models\Sip;
use App\Models\StrPegawai;
use App\Services\Hrd\HrdRiwayatKarirService;
use App\Services\Notification\WhatsappService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdRiwayatKarirController extends Controller
{
    public function __construct(
        private readonly HrdRiwayatKarirService $service,
        private readonly WhatsappService $whatsapp,
    ) {}

    // ── Jabatan ───────────────────────────────────────────────────────────────

    public function jabatan(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat jabatan berhasil diambil.', 'data' => $this->service->getJabatan($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeJabatan(StoreJabatanRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createJabatan($id, $request->validated(), $request->file('sk_jabatan'));
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function updateJabatan(UpdateJabatanRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updateJabatan($riwayatId, $id, $request->validated(), $request->file('sk_jabatan'));
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroyJabatan(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->deleteJabatan($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    // ── STR ───────────────────────────────────────────────────────────────────

    public function str(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat STR berhasil diambil.', 'data' => $this->service->getStr($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeStr(StoreStrRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createStr($id, $request->validated(), $request->file('sk_str'));
            return response()->json(['success' => true, 'message' => 'Riwayat STR berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function updateStr(UpdateStrRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updateStr($riwayatId, $id, $request->validated(), $request->file('sk_str'));
            return response()->json(['success' => true, 'message' => 'Riwayat STR berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroyStr(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->deleteStr($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat STR berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    // ── SIP ───────────────────────────────────────────────────────────────────

    public function sip(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat SIP berhasil diambil.', 'data' => $this->service->getSip($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeSip(StoreSipRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createSip($id, $request->validated(), $request->file('sk_sip'));
            return response()->json(['success' => true, 'message' => 'Riwayat SIP berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function updateSip(UpdateSipRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updateSip($riwayatId, $id, $request->validated(), $request->file('sk_sip'));
            return response()->json(['success' => true, 'message' => 'Riwayat SIP berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroySip(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->deleteSip($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat SIP berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    // ── Penugasan Klinis ──────────────────────────────────────────────────────

    public function penugasanKlinis(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat penugasan klinis berhasil diambil.', 'data' => $this->service->getPenugasanKlinis($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storePenugasanKlinis(StorePenugasanKlinisRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createPenugasanKlinis($id, $request->validated(), $request->file('dokumen_file'));
            return response()->json(['success' => true, 'message' => 'Riwayat penugasan klinis berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function updatePenugasanKlinis(UpdatePenugasanKlinisRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updatePenugasanKlinis($riwayatId, $id, $request->validated(), $request->file('dokumen_file'));
            return response()->json(['success' => true, 'message' => 'Riwayat penugasan klinis berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroyPenugasanKlinis(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->deletePenugasanKlinis($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat penugasan klinis berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    // ── Pangkat ───────────────────────────────────────────────────────────────

    public function pangkat(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat pangkat berhasil diambil.', 'data' => $this->service->getPangkat($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storePangkat(StorePangkatRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createPangkat($id, $request->validated(), $request->file('sk_pangkat'));
            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function updatePangkat(UpdatePangkatRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updatePangkat($riwayatId, $id, $request->validated(), $request->file('sk_pangkat'));
            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroyPangkat(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->deletePangkat($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    // ── Reminder WhatsApp ─────────────────────────────────────────────────────

    public function sendReminderStrSip(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'tipe_dokumen' => 'required|in:str,sip',
                'dokumen_id'   => 'required|integer',
            ]);

            $tipe  = $request->input('tipe_dokumen');
            $docId = (int) $request->input('dokumen_id');

            if ($tipe === 'str') {
                $doc         = StrPegawai::with('pegawai.pribadi')->where('id', $docId)->where('pegawai_id', $id)->firstOrFail();
                $namaDokumen = 'STR (' . $doc->nomor_str . ')';
                $kadaluarsa  = $doc->tanggal_kadaluarsa;
                $filePath    = $doc->sk_file_path;
            } else {
                $doc         = Sip::with('pegawai.pribadi')->where('id', $docId)->where('pegawai_id', $id)->firstOrFail();
                $namaDokumen = 'SIP (' . $doc->nomor_sip . ')';
                $kadaluarsa  = $doc->tanggal_kadaluarsa;
                $filePath    = $doc->sk_file_path;
            }

            return $this->processWaReminder($doc, $namaDokumen, $kadaluarsa, $filePath);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data dokumen tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function sendReminderPenugasanKlinis(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'dokumen_id' => 'required|integer',
            ]);

            $docId = (int) $request->input('dokumen_id');
            $doc   = PenugasanKlinis::with('pegawai.pribadi')->where('id', $docId)->where('pegawai_id', $id)->firstOrFail();

            return $this->processWaReminder(
                $doc,
                'Penugasan Klinis (' . $doc->nomor_surat . ')',
                $doc->tgl_kadaluarsa,
                $doc->dokumen_file_path,
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data dokumen tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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
            $urgensi = "🚨 *SANGAT MENDESAK* 🚨\nDokumen {$namaDokumen} Anda *TELAH KEDALUWARSA* sejak " . abs($selisihHari) . " hari yang lalu ({$kadaluarsa->format('d-m-Y')}).";
        } elseif ($selisihHari <= 30) {
            $urgensi = "⚠️ *PENGINGAT PENTING* ⚠️\nDokumen {$namaDokumen} Anda akan segera kedaluwarsa dalam *{$selisihHari} hari* ({$kadaluarsa->format('d-m-Y')}).";
        } else {
            $urgensi = "ℹ️ *INFORMASI* ℹ️\nDokumen {$namaDokumen} Anda masih aktif hingga {$kadaluarsa->format('d-m-Y')}, namun kami mengingatkan Anda untuk mengecek kembali statusnya.";
        }

        $pesan = "Halo {$doc->pegawai->nama},\n\n{$urgensi}\n\nHarap segera berkoordinasi dengan pihak HRD untuk melakukan pembaruan dokumen demi kelancaran operasional RS.";

        if ($filePath) {
            $pesan .= "\n\nAnda dapat meninjau dokumen lama Anda pada tautan berikut:\n" . asset($filePath);
        }

        $result = $this->whatsapp->sendMessage($this->formatPhoneNumber($noTelp), $pesan);

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim pesan: ' . ($result['message'] ?? 'Terjadi kesalahan.')], 422);
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
            return '62' . substr($no, 1);
        }
        if (str_starts_with($no, '62')) {
            return $no;
        }
        return '62' . $no;
    }
}
