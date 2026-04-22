<?php

namespace App\Services\Profile;

use App\Models\JenisPegawai;
use App\Models\Profesi;
use App\Models\PerubahanData;
use App\Repositories\Profile\PegawaiProfileRepository;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProfileService
{
    public function __construct(
        private readonly AdminService $adminService,
        private readonly PegawaiService $pegawaiService,
        private readonly HrdService $hrdService,
        private readonly DirekturService $direkturService,
        private readonly PegawaiProfileRepository $pegawaiProfileRepository,
    ) {
    }

    public function getPayloadByRole(string $role, int $userId): ?array
    {
        return match ($role) {
            'admin' => $this->adminService->build($userId),
            'pegawai' => $this->pegawaiService->build($userId),
            'hrd' => $this->hrdService->build($userId),
            'direktur' => $this->direkturService->build($userId),
            default => null,
        };
    }

    /**
     * Simpan pengajuan perubahan data (header + detail) tanpa langsung mengubah tabel master.
     *
     * Format $details:
     * [
     *   [
     *     'target_table' => 'pegawai_pribadi',
     *     'kolom' => 'alamat',
     *     'value' => 'Alamat baru',
     *     'old_value' => 'Alamat lama',
     *   ],
     * ]
     */
    public function updateWithAgreement(int $byUser, string $fitur, ?string $note, array $details): PerubahanData
    {
        $fitur = trim($fitur);

        if ($byUser <= 0) {
            throw new InvalidArgumentException('byUser tidak valid.');
        }

        if ($fitur === '') {
            throw new InvalidArgumentException('fitur wajib diisi.');
        }

        if (empty($details)) {
            throw new InvalidArgumentException('details perubahan tidak boleh kosong.');
        }

        return DB::transaction(function () use ($byUser, $fitur, $note, $details) {
            $perubahanData = PerubahanData::query()->create([
                'by_user' => $byUser,
                'fitur' => $fitur,
                'status' => 'pending',
                'note' => $note,
            ]);

            $detailRows = [];

            foreach ($details as $index => $detail) {
                if (! is_array($detail)) {
                    throw new InvalidArgumentException("detail index {$index} harus berupa array.");
                }

                $targetTable = trim((string) ($detail['target_table'] ?? ''));
                $kolom = trim((string) ($detail['kolom'] ?? ''));

                if ($targetTable === '') {
                    throw new InvalidArgumentException("target_table wajib diisi pada detail index {$index}.");
                }

                if ($kolom === '') {
                    throw new InvalidArgumentException("kolom wajib diisi pada detail index {$index}.");
                }

                $detailRows[] = [
                    'target_table' => $targetTable,
                    'kolom' => $kolom,
                    'value' => $this->stringifyValue($detail['value'] ?? null),
                    'old_value' => $this->stringifyValue($detail['old_value'] ?? null),
                ];
            }

            $perubahanData->details()->createMany($detailRows);

            return $perubahanData->load('details');
        });
    }

    public function submitProfileUpdateWithAgreement(int $byUser, array $payload, ?string $note = null): PerubahanData
    {
        $user = $this->pegawaiProfileRepository->findUserWithPegawaiPribadiById($byUser);

        if ($user === null || $user->pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $pegawai = $user->pegawai;
        $pribadi = $pegawai->pribadi;

        $details = [];

        $this->appendDetailIfChanged($details, 'pegawai', 'nip', $pegawai->nip, $payload, 'nip');
        $this->appendDetailIfChanged($details, 'pegawai', 'nik', $pegawai->nik, $payload, 'nik');
        $this->appendDetailIfChanged($details, 'pegawai', 'nama', $pegawai->nama, $payload, 'nama');
        $this->appendDetailIfChanged($details, 'pegawai', 'status_pegawai', $pegawai->status_pegawai, $payload, 'status_pegawai');
        $this->appendDetailIfChangedDate($details, 'pegawai', 'tgl_masuk', $pegawai->tgl_masuk?->toDateString(), $payload, 'tgl_masuk');
        $this->appendDetailIfChangedDate($details, 'pegawai', 'tmt_cpns', $pegawai->tmt_cpns?->toDateString(), $payload, 'tmt_cpns');
        $this->appendDetailIfChangedDate($details, 'pegawai', 'tmt_pns', $pegawai->tmt_pns?->toDateString(), $payload, 'tmt_pns');

        if (array_key_exists('profesi', $payload)) {
            $newProfesiId = $this->resolveProfesiId($payload['profesi']);
            if ((int) ($pegawai->profesi_id ?? 0) !== $newProfesiId) {
                $details[] = [
                    'target_table' => 'pegawai',
                    'kolom' => 'profesi_id',
                    'old_value' => $pegawai->profesi_id,
                    'value' => $newProfesiId,
                ];
            }
        }

        if (array_key_exists('jenis_pegawai', $payload)) {
            $newJenisPegawaiId = $this->resolveJenisPegawaiId($payload['jenis_pegawai']);
            if ((int) ($pegawai->jenis_pegawai_id ?? 0) !== $newJenisPegawaiId) {
                $details[] = [
                    'target_table' => 'pegawai',
                    'kolom' => 'jenis_pegawai_id',
                    'old_value' => $pegawai->jenis_pegawai_id,
                    'value' => $newJenisPegawaiId,
                ];
            }
        }

        $this->appendDetailIfChanged($details, 'pegawai_pribadi', 'jenis_kelamin', $pribadi?->jenis_kelamin, $payload, 'jenis_kelamin');
        $this->appendDetailIfChangedDate($details, 'pegawai_pribadi', 'tanggal_lahir', $pribadi?->tanggal_lahir?->toDateString(), $payload, 'tanggal_lahir');
        $this->appendDetailIfChanged($details, 'pegawai_pribadi', 'agama', $pribadi?->agama, $payload, 'agama');
        $this->appendDetailIfChanged($details, 'pegawai_pribadi', 'status_perkawinan', $pribadi?->status_perkawinan, $payload, 'status_kawin');
        $this->appendDetailIfChanged($details, 'pegawai_pribadi', 'alamat', $pribadi?->alamat, $payload, 'alamat');
        $this->appendDetailIfChanged($details, 'pegawai_pribadi', 'no_telp', $pribadi?->no_telp, $payload, 'no_telp');
        $this->appendDetailIfChanged($details, 'pegawai_pribadi', 'no_kk', $pribadi?->no_kk, $payload, 'no_kk');
        $this->appendDetailIfChanged($details, 'pegawai_pribadi', 'email', $pribadi?->email, $payload, 'email');

        if (empty($details)) {
            throw new InvalidArgumentException('Tidak ada perubahan data yang bisa diajukan.');
        }

        return $this->updateWithAgreement(
            byUser: $byUser,
            fitur: 'profile',
            note: $note,
            details: $details,
        );
    }

    public function updateProfilePicture(int $byUser, ?UploadedFile $file): array
    {
        if ($byUser <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        if ($file === null) {
            throw new InvalidArgumentException('File foto wajib diupload.');
        }

        $user = $this->pegawaiProfileRepository->findUserWithPegawaiPribadiById($byUser);

        if ($user === null || $user->pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $pegawai = $user->pegawai;
        $pribadi = $pegawai->pribadi;

        if ($pribadi === null) {
            $pribadi = $this->pegawaiProfileRepository->createPegawaiPribadi((int) $pegawai->id);
        }

        $folder = public_path('dokumen/foto');
        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = sprintf(
            'profile-%d-%d.%s',
            (int) $pegawai->id,
            time(),
            $file->getClientOriginalExtension()
        );

        $file->move($folder, $filename);
        $newPath = 'dokumen/foto/'.$filename;

        $oldPath = trim((string) ($pribadi->foto_path ?? ''));
        if ($oldPath !== '' && ! str_starts_with($oldPath, 'http://') && ! str_starts_with($oldPath, 'https://')) {
            $oldAbsolutePath = public_path(ltrim($oldPath, '/'));
            if (is_file($oldAbsolutePath)) {
                @unlink($oldAbsolutePath);
            }
        }

        $pribadi->foto_path = $newPath;
        $this->pegawaiProfileRepository->savePegawaiPribadi($pribadi);

        return [
            'foto_path' => $newPath,
            'link_photo_profile' => url('/'.$newPath),
            'updated_at' => optional($pribadi->updated_at)?->toDateTimeString(),
        ];
    }

    public function updateKtpFile(int $byUser, ?UploadedFile $file): array
    {
        if ($byUser <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        if ($file === null) {
            throw new InvalidArgumentException('File KTP wajib diupload.');
        }

        $user = $this->pegawaiProfileRepository->findUserWithPegawaiPribadiById($byUser);

        if ($user === null || $user->pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $pegawai = $user->pegawai;
        $pribadi = $pegawai->pribadi;

        if ($pribadi === null) {
            $pribadi = $this->pegawaiProfileRepository->createPegawaiPribadi((int) $pegawai->id);
        }

        $folder = public_path('dokumen/ktp');
        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = sprintf(
            'ktp-%d-%d.%s',
            (int) $pegawai->id,
            time(),
            $file->getClientOriginalExtension()
        );

        $file->move($folder, $filename);
        $newPath = 'dokumen/ktp/'.$filename;

        $oldPath = trim((string) ($pribadi->ktp_file_path ?? ''));
        if ($oldPath !== '' && ! str_starts_with($oldPath, 'http://') && ! str_starts_with($oldPath, 'https://')) {
            $oldAbsolutePath = public_path(ltrim($oldPath, '/'));
            if (is_file($oldAbsolutePath)) {
                @unlink($oldAbsolutePath);
            }
        }

        $pribadi->ktp_file_path = $newPath;
        $this->pegawaiProfileRepository->savePegawaiPribadi($pribadi);

        return [
            'ktp_file_path' => $newPath,
            'link_ktp_file' => url('/'.$newPath),
            'updated_at' => optional($pribadi->updated_at)?->toDateTimeString(),
        ];
    }

    public function updateKkFile(int $byUser, ?UploadedFile $file): array
    {
        if ($byUser <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        if ($file === null) {
            throw new InvalidArgumentException('File KK wajib diupload.');
        }

        $user = $this->pegawaiProfileRepository->findUserWithPegawaiPribadiById($byUser);

        if ($user === null || $user->pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $pegawai = $user->pegawai;
        $pribadi = $pegawai->pribadi;

        if ($pribadi === null) {
            $pribadi = $this->pegawaiProfileRepository->createPegawaiPribadi((int) $pegawai->id);
        }

        $folder = public_path('dokumen/kk');
        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = sprintf(
            'kk-%d-%d.%s',
            (int) $pegawai->id,
            time(),
            $file->getClientOriginalExtension()
        );

        $file->move($folder, $filename);
        $newPath = 'dokumen/kk/'.$filename;

        $oldPath = trim((string) ($pribadi->kk_file_path ?? ''));
        if ($oldPath !== '' && ! str_starts_with($oldPath, 'http://') && ! str_starts_with($oldPath, 'https://')) {
            $oldAbsolutePath = public_path(ltrim($oldPath, '/'));
            if (is_file($oldAbsolutePath)) {
                @unlink($oldAbsolutePath);
            }
        }

        $pribadi->kk_file_path = $newPath;
        $pribadi->link_kk = url('/'.$newPath);
        $this->pegawaiProfileRepository->savePegawaiPribadi($pribadi);

        return [
            'kk_file_path' => $newPath,
            'link_kk' => (string) ($pribadi->link_kk ?? ''),
            'updated_at' => optional($pribadi->updated_at)?->toDateTimeString(),
        ];
    }

    private function appendDetailIfChanged(array &$details, string $targetTable, string $kolom, mixed $oldValue, array $payload, string $payloadKey): void
    {
        if (! array_key_exists($payloadKey, $payload)) {
            return;
        }

        $newValue = $this->normalizeStringValue($payload[$payloadKey]);
        $oldValue = $this->normalizeStringValue($oldValue);

        if ($oldValue === $newValue) {
            return;
        }

        $details[] = [
            'target_table' => $targetTable,
            'kolom' => $kolom,
            'old_value' => $oldValue,
            'value' => $newValue,
        ];
    }

    private function appendDetailIfChangedDate(array &$details, string $targetTable, string $kolom, ?string $oldValue, array $payload, string $payloadKey): void
    {
        if (! array_key_exists($payloadKey, $payload)) {
            return;
        }

        $newDate = $this->normalizeDateValue($payload[$payloadKey]);
        $oldDate = $this->normalizeDateValue($oldValue);

        if ($oldDate === $newDate) {
            return;
        }

        $details[] = [
            'target_table' => $targetTable,
            'kolom' => $kolom,
            'old_value' => $oldDate,
            'value' => $newDate,
        ];
    }

    private function normalizeStringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private function normalizeDateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse((string) $value)->toDateString();
    }

    private function resolveProfesiId(mixed $value): int
    {
        if ($value === null || $value === '') {
            throw new InvalidArgumentException('profesi wajib diisi jika field profesi dikirim.');
        }

        if (is_numeric($value)) {
            $profesi = Profesi::query()->find((int) $value);
        } else {
            $profesi = Profesi::query()->where('nama', (string) $value)->first();
        }

        if ($profesi === null) {
            throw new InvalidArgumentException('Referensi profesi tidak ditemukan.');
        }

        return (int) $profesi->id;
    }

    private function resolveJenisPegawaiId(mixed $value): int
    {
        if ($value === null || $value === '') {
            throw new InvalidArgumentException('jenis_pegawai wajib diisi jika field jenis_pegawai dikirim.');
        }

        if (is_numeric($value)) {
            $jenisPegawai = JenisPegawai::query()->find((int) $value);
        } else {
            $jenisPegawai = JenisPegawai::query()->where('nama', (string) $value)->first();
        }

        if ($jenisPegawai === null) {
            throw new InvalidArgumentException('Referensi jenis_pegawai tidak ditemukan.');
        }

        return (int) $jenisPegawai->id;
    }

    private function stringifyValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
