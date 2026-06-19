<?php

namespace App\Services\Hrd;

use App\Repositories\Hrd\HrdRiwayatKarirRepository;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HrdRiwayatKarirService
{
    public function __construct(private readonly HrdRiwayatKarirRepository $repository) {}

    private function getPegawaiOrFail(int $pegawaiId)
    {
        $pegawai = $this->repository->findPegawaiById($pegawaiId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai tidak ditemukan.');
        }
        return $pegawai;
    }

    private function handleFileUpload(string $folder, string $prefix, int $pegawaiId, ?UploadedFile $file, ?string $oldPath): ?string
    {
        if ($file === null) {
            return null;
        }

        $fullFolder = public_path($folder);
        if (!is_dir($fullFolder)) {
            mkdir($fullFolder, 0755, true);
        }

        if ($oldPath && file_exists(public_path($oldPath))) {
            @unlink(public_path($oldPath));
        }

        $filename = sprintf('%s-%d-%d.%s', $prefix, $pegawaiId, time(), $file->getClientOriginalExtension());
        $file->move($fullFolder, $filename);

        return $folder.'/'.$filename;
    }

    // ── Jabatan ───────────────────────────────────────────────────────────────

    public function getJabatan(int $pegawaiId): array
    {
        $items = $this->repository->getJabatanByPegawaiId($pegawaiId)
            ->map(fn ($jp) => $this->formatJabatan($jp))->values()->toArray();

        return ['label' => 'Riwayat jabatan', 'total' => count($items), 'items' => $items];
    }

    public function createJabatan(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $skFilePath = $this->handleFileUpload('dokumen/jabatan', 'sk-jabatan', $pegawaiId, $file, null);

        $jabatanData = [
            'unit_kerja_id' => $payload['unit_kerja_id'] ?? null,
            'nama'          => (string) $payload['nama_jabatan'],
            'tmt_mulai'     => $payload['tmt_mulai'] ?? null,
            'tmt_selesai'   => $payload['tmt_selesai'] ?? null,
            'sk_file_path'  => $skFilePath,
        ];

        $pivotData = [
            'is_current' => (bool) $payload['is_current'],
            'started_at' => $payload['tmt_mulai'] ?? null,
            'ended_at'   => $payload['tmt_selesai'] ?? null,
            'note'       => $payload['note'] ?? null,
        ];

        $jp = $this->repository->createJabatanAndPivot($pegawai, $jabatanData, $pivotData);

        return $this->formatJabatan($jp->load('jabatan.unitKerja'));
    }

    public function updateJabatan(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $jp = $this->repository->findJabatanByIdAndPegawaiId($id, $pegawaiId);
        if ($jp === null) {
            throw new ModelNotFoundException('Data riwayat jabatan tidak ditemukan.');
        }

        $jabatanData = [];
        if (array_key_exists('unit_kerja_id', $payload)) $jabatanData['unit_kerja_id'] = $payload['unit_kerja_id'];
        if (array_key_exists('nama_jabatan', $payload))  $jabatanData['nama'] = (string) $payload['nama_jabatan'];
        if (array_key_exists('tmt_mulai', $payload))     $jabatanData['tmt_mulai'] = $payload['tmt_mulai'];
        if (array_key_exists('tmt_selesai', $payload))   $jabatanData['tmt_selesai'] = $payload['tmt_selesai'];

        $pivotData = [];
        if (array_key_exists('is_current', $payload)) $pivotData['is_current'] = (bool) $payload['is_current'];
        if (array_key_exists('tmt_mulai', $payload))  $pivotData['started_at'] = $payload['tmt_mulai'];
        if (array_key_exists('tmt_selesai', $payload)) $pivotData['ended_at'] = $payload['tmt_selesai'];
        if (array_key_exists('note', $payload))        $pivotData['note'] = $payload['note'];

        if ($file !== null) {
            $newPath = $this->handleFileUpload('dokumen/jabatan', 'sk-jabatan', $pegawaiId, $file, $jp->jabatan?->sk_file_path);
            $jabatanData['sk_file_path'] = $newPath;
        }

        $updated = $this->repository->updateJabatanAndPivot($jp, $jabatanData, $pivotData);

        return $this->formatJabatan($updated);
    }

    public function deleteJabatan(int $id, int $pegawaiId): array
    {
        $jp = $this->repository->findJabatanByIdAndPegawaiId($id, $pegawaiId);
        if ($jp === null) {
            throw new ModelNotFoundException('Data riwayat jabatan tidak ditemukan.');
        }

        $skPath = $jp->jabatan?->sk_file_path;
        if ($skPath && file_exists(public_path($skPath))) {
            @unlink(public_path($skPath));
        }

        $this->repository->deleteJabatanAndPivot($jp);

        return ['id' => $id];
    }

    private function formatJabatan($item): array
    {
        return [
            'id'           => $item->id,
            'unit_kerja_id'  => $item->jabatan?->unit_kerja_id,
            'unit_kerja_nama' => $item->jabatan?->unitKerja?->nama ?? '',
            'nama_jabatan' => $item->jabatan?->nama ?? '',
            'is_current'   => (bool) $item->is_current,
            'tmt_mulai'    => $item->jabatan?->tmt_mulai?->format('Y-m-d') ?? $item->started_at?->format('Y-m-d'),
            'tmt_selesai'  => $item->jabatan?->tmt_selesai?->format('Y-m-d') ?? $item->ended_at?->format('Y-m-d'),
            'link_sk'      => $item->jabatan?->sk_file_path ? '/'.$item->jabatan->sk_file_path : null,
            'note'         => $item->note ?? '',
        ];
    }

    // ── STR ───────────────────────────────────────────────────────────────────

    public function getStr(int $pegawaiId): array
    {
        $items = $this->repository->getStrByPegawaiId($pegawaiId)
            ->map(fn ($s) => $this->formatStr($s))->values()->toArray();

        return ['label' => 'Riwayat STR', 'total' => count($items), 'items' => $items];
    }

    public function createStr(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $skFilePath = $this->handleFileUpload('dokumen/str', 'sk-str', $pegawaiId, $file, null);

        $data = [
            'nomor_str'          => $payload['nomor_str'],
            'tanggal_terbit'     => $payload['tanggal_terbit'],
            'tanggal_kadaluarsa' => $payload['tanggal_kadaluarsa'] ?? null,
            'is_current'         => (bool) $payload['is_current'],
            'sk_file_path'       => $skFilePath,
        ];

        $str = $this->repository->createStr($pegawai, $data);

        return $this->formatStr($str);
    }

    public function updateStr(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $str = $this->repository->findStrByIdAndPegawaiId($id, $pegawaiId);
        if ($str === null) {
            throw new ModelNotFoundException('Data riwayat STR tidak ditemukan.');
        }

        $data = [];
        if (array_key_exists('nomor_str', $payload))          $data['nomor_str'] = $payload['nomor_str'];
        if (array_key_exists('tanggal_terbit', $payload))     $data['tanggal_terbit'] = $payload['tanggal_terbit'];
        if (array_key_exists('tanggal_kadaluarsa', $payload)) $data['tanggal_kadaluarsa'] = $payload['tanggal_kadaluarsa'];
        if (array_key_exists('is_current', $payload))         $data['is_current'] = (bool) $payload['is_current'];

        if ($file !== null) {
            $data['sk_file_path'] = $this->handleFileUpload('dokumen/str', 'sk-str', $pegawaiId, $file, $str->sk_file_path);
        }

        $updated = $this->repository->updateStr($str, $data);

        return $this->formatStr($updated);
    }

    public function deleteStr(int $id, int $pegawaiId): array
    {
        $str = $this->repository->findStrByIdAndPegawaiId($id, $pegawaiId);
        if ($str === null) {
            throw new ModelNotFoundException('Data riwayat STR tidak ditemukan.');
        }

        if ($str->sk_file_path && file_exists(public_path($str->sk_file_path))) {
            @unlink(public_path($str->sk_file_path));
        }

        $this->repository->deleteStr($str);

        return ['id' => $id];
    }

    private function formatStr($item): array
    {
        return [
            'id'                 => $item->id,
            'nomor_str'          => $item->nomor_str,
            'tanggal_terbit'     => $item->tanggal_terbit?->format('Y-m-d'),
            'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa?->format('Y-m-d'),
            'is_current'         => (bool) $item->is_current,
            'link_sk'            => $item->sk_file_path ? '/'.$item->sk_file_path : null,
        ];
    }

    // ── SIP ───────────────────────────────────────────────────────────────────

    public function getSip(int $pegawaiId): array
    {
        $items = $this->repository->getSipByPegawaiId($pegawaiId)
            ->map(fn ($s) => $this->formatSip($s))->values()->toArray();

        return ['label' => 'Riwayat SIP', 'total' => count($items), 'items' => $items];
    }

    public function createSip(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $skFilePath = $this->handleFileUpload('dokumen/sip', 'sk-sip', $pegawaiId, $file, null);

        $data = [
            'jenis_sip_id'       => $payload['jenis_sip_id'] ?? null,
            'nomor_sip'          => $payload['nomor_sip'],
            'tanggal_terbit'     => $payload['tanggal_terbit'],
            'tanggal_kadaluarsa' => $payload['tanggal_kadaluarsa'] ?? null,
            'is_current'         => (bool) $payload['is_current'],
            'sk_file_path'       => $skFilePath,
        ];

        $sip = $this->repository->createSip($pegawai, $data);

        return $this->formatSip($sip);
    }

    public function updateSip(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $sip = $this->repository->findSipByIdAndPegawaiId($id, $pegawaiId);
        if ($sip === null) {
            throw new ModelNotFoundException('Data riwayat SIP tidak ditemukan.');
        }

        $data = [];
        if (array_key_exists('jenis_sip_id', $payload))       $data['jenis_sip_id'] = $payload['jenis_sip_id'];
        if (array_key_exists('nomor_sip', $payload))           $data['nomor_sip'] = $payload['nomor_sip'];
        if (array_key_exists('tanggal_terbit', $payload))      $data['tanggal_terbit'] = $payload['tanggal_terbit'];
        if (array_key_exists('tanggal_kadaluarsa', $payload))  $data['tanggal_kadaluarsa'] = $payload['tanggal_kadaluarsa'];
        if (array_key_exists('is_current', $payload))          $data['is_current'] = (bool) $payload['is_current'];

        if ($file !== null) {
            $data['sk_file_path'] = $this->handleFileUpload('dokumen/sip', 'sk-sip', $pegawaiId, $file, $sip->sk_file_path);
        }

        $updated = $this->repository->updateSip($sip, $data);

        return $this->formatSip($updated);
    }

    public function deleteSip(int $id, int $pegawaiId): array
    {
        $sip = $this->repository->findSipByIdAndPegawaiId($id, $pegawaiId);
        if ($sip === null) {
            throw new ModelNotFoundException('Data riwayat SIP tidak ditemukan.');
        }

        if ($sip->sk_file_path && file_exists(public_path($sip->sk_file_path))) {
            @unlink(public_path($sip->sk_file_path));
        }

        $this->repository->deleteSip($sip);

        return ['id' => $id];
    }

    private function formatSip($item): array
    {
        return [
            'id'                 => $item->id,
            'jenis_sip_id'       => $item->jenis_sip_id,
            'jenis_sip_nama'     => $item->jenisSip?->nama ?? '',
            'nomor_sip'          => $item->nomor_sip,
            'tanggal_terbit'     => $item->tanggal_terbit?->format('Y-m-d'),
            'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa?->format('Y-m-d'),
            'is_current'         => (bool) $item->is_current,
            'link_sk'            => $item->sk_file_path ? '/'.$item->sk_file_path : null,
        ];
    }

    // ── Penugasan Klinis ──────────────────────────────────────────────────────

    public function getPenugasanKlinis(int $pegawaiId): array
    {
        $items = $this->repository->getPenugasanKlinisByPegawaiId($pegawaiId)
            ->map(fn ($pk) => $this->formatPenugasanKlinis($pk))->values()->toArray();

        return ['label' => 'Riwayat Penugasan Klinis', 'total' => count($items), 'items' => $items];
    }

    public function createPenugasanKlinis(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $filePath = $this->handleFileUpload('dokumen/penugasan-klinis', 'sk-penugasan-klinis', $pegawaiId, $file, null);

        $data = [
            'nomor_surat'      => $payload['nomor_surat'],
            'tgl_mulai'        => $payload['tgl_mulai'],
            'tgl_kadaluarsa'   => $payload['tgl_kadaluarsa'] ?? null,
            'is_current'       => (bool) $payload['is_current'],
            'dokumen_file_path' => $filePath,
        ];

        $pk = $this->repository->createPenugasanKlinis($pegawai, $data);

        return $this->formatPenugasanKlinis($pk);
    }

    public function updatePenugasanKlinis(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pk = $this->repository->findPenugasanKlinisByIdAndPegawaiId($id, $pegawaiId);
        if ($pk === null) {
            throw new ModelNotFoundException('Data riwayat penugasan klinis tidak ditemukan.');
        }

        $data = [];
        if (array_key_exists('nomor_surat', $payload))    $data['nomor_surat'] = $payload['nomor_surat'];
        if (array_key_exists('tgl_mulai', $payload))      $data['tgl_mulai'] = $payload['tgl_mulai'];
        if (array_key_exists('tgl_kadaluarsa', $payload)) $data['tgl_kadaluarsa'] = $payload['tgl_kadaluarsa'];
        if (array_key_exists('is_current', $payload))     $data['is_current'] = (bool) $payload['is_current'];

        if ($file !== null) {
            $data['dokumen_file_path'] = $this->handleFileUpload('dokumen/penugasan-klinis', 'sk-penugasan-klinis', $pegawaiId, $file, $pk->dokumen_file_path);
        }

        $updated = $this->repository->updatePenugasanKlinis($pk, $data);

        return $this->formatPenugasanKlinis($updated);
    }

    public function deletePenugasanKlinis(int $id, int $pegawaiId): array
    {
        $pk = $this->repository->findPenugasanKlinisByIdAndPegawaiId($id, $pegawaiId);
        if ($pk === null) {
            throw new ModelNotFoundException('Data riwayat penugasan klinis tidak ditemukan.');
        }

        if ($pk->dokumen_file_path && file_exists(public_path($pk->dokumen_file_path))) {
            @unlink(public_path($pk->dokumen_file_path));
        }

        $this->repository->deletePenugasanKlinis($pk);

        return ['id' => $id];
    }

    private function formatPenugasanKlinis($item): array
    {
        return [
            'id'             => $item->id,
            'nomor_surat'    => $item->nomor_surat,
            'tgl_mulai'      => $item->tgl_mulai?->format('Y-m-d'),
            'tgl_kadaluarsa' => $item->tgl_kadaluarsa?->format('Y-m-d'),
            'is_current'     => (bool) $item->is_current,
            'link_dokumen'   => $item->dokumen_file_path ? '/'.$item->dokumen_file_path : null,
        ];
    }

    // ── Pangkat ───────────────────────────────────────────────────────────────

    public function getPangkat(int $pegawaiId): array
    {
        $items = $this->repository->getPangkatByPegawaiId($pegawaiId)
            ->map(fn ($pp) => $this->formatPangkat($pp))->values()->toArray();

        return ['label' => 'Riwayat pangkat', 'total' => count($items), 'items' => $items];
    }

    public function createPangkat(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $skFilePath = $this->handleFileUpload('dokumen/pangkat', 'sk-pangkat', $pegawaiId, $file, null);

        $pangkatData = [
            'nama'           => (string) $payload['nama_pangkat'],
            'pejabat_penetap' => $payload['pejabat_penetap'] ?? null,
            'tmt_sk'         => $payload['tmt_sk'] ?? null,
            'sk_file_path'   => $skFilePath,
        ];

        $pivotData = [
            'is_current' => (bool) $payload['is_current'],
            'started_at' => $payload['started_at'] ?? null,
            'ended_at'   => $payload['ended_at'] ?? null,
            'note'       => $payload['note'] ?? null,
        ];

        $pp = $this->repository->createPangkatAndPivot($pegawai, $pangkatData, $pivotData);

        return $this->formatPangkat($pp);
    }

    public function updatePangkat(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pp = $this->repository->findPangkatByIdAndPegawaiId($id, $pegawaiId);
        if ($pp === null) {
            throw new ModelNotFoundException('Data riwayat pangkat tidak ditemukan.');
        }

        $pangkatData = [];
        if (array_key_exists('nama_pangkat', $payload))    $pangkatData['nama'] = (string) $payload['nama_pangkat'];
        if (array_key_exists('pejabat_penetap', $payload)) $pangkatData['pejabat_penetap'] = $payload['pejabat_penetap'];
        if (array_key_exists('tmt_sk', $payload))          $pangkatData['tmt_sk'] = $payload['tmt_sk'];

        $pivotData = [];
        if (array_key_exists('is_current', $payload)) $pivotData['is_current'] = (bool) $payload['is_current'];
        if (array_key_exists('started_at', $payload)) $pivotData['started_at'] = $payload['started_at'];
        if (array_key_exists('ended_at', $payload))   $pivotData['ended_at'] = $payload['ended_at'];
        if (array_key_exists('note', $payload))        $pivotData['note'] = $payload['note'];

        if ($file !== null) {
            $pangkatData['sk_file_path'] = $this->handleFileUpload('dokumen/pangkat', 'sk-pangkat', $pegawaiId, $file, $pp->pangkat?->sk_file_path);
        }

        $updated = $this->repository->updatePangkatAndPivot($pp, $pangkatData, $pivotData);

        return $this->formatPangkat($updated);
    }

    public function deletePangkat(int $id, int $pegawaiId): array
    {
        $pp = $this->repository->findPangkatByIdAndPegawaiId($id, $pegawaiId);
        if ($pp === null) {
            throw new ModelNotFoundException('Data riwayat pangkat tidak ditemukan.');
        }

        $skPath = $pp->pangkat?->sk_file_path;
        if ($skPath && file_exists(public_path($skPath))) {
            @unlink(public_path($skPath));
        }

        $this->repository->deletePangkatAndPivot($pp);

        return ['id' => $id];
    }

    private function formatPangkat($item): array
    {
        return [
            'id'              => $item->id,
            'nama_pangkat'    => $item->pangkat?->nama ?? '',
            'is_current'      => (bool) $item->is_current,
            'pejabat_penetap' => $item->pangkat?->pejabat_penetap,
            'tmt_sk'          => $item->pangkat?->tmt_sk?->format('Y-m-d'),
            'started_at'      => $item->started_at?->format('Y-m-d'),
            'ended_at'        => $item->ended_at?->format('Y-m-d'),
            'link_sk'         => $item->pangkat?->sk_file_path ? '/'.$item->pangkat->sk_file_path : null,
            'note'            => $item->note ?? '',
        ];
    }

    // ── Pendidikan ────────────────────────────────────────────────────────────

    public function getPendidikan(int $pegawaiId): array
    {
        $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
        $items = $pribadi
            ? $this->repository->getPendidikanByPribadiId($pribadi->id)
                ->map(fn ($p) => $this->formatPendidikan($p))->values()->toArray()
            : [];

        return ['label' => 'Riwayat pendidikan', 'total' => count($items), 'items' => $items];
    }

    public function createPendidikan(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $this->getPegawaiOrFail($pegawaiId);

        $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId)
            ?? $this->repository->createPribadi($pegawaiId);

        $ijazahPath = $this->handleFileUpload('dokumen/ijazah', 'ijazah', $pegawaiId, $file, null);

        $data = [
            'jenjang'           => $payload['jenjang'],
            'institusi'         => $payload['institusi'],
            'jurusan'           => $payload['jurusan'],
            'tahun_lulus'       => (int) $payload['tahun_lulus'],
            'nomor_ijazah'      => $payload['nomor_ijazah'] ?? null,
            'ijazah_file_path'  => $ijazahPath,
        ];

        $pendidikan = $this->repository->createPendidikan($pribadi->id, $data);

        return $this->formatPendidikan($pendidikan);
    }

    public function updatePendidikan(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            throw new ModelNotFoundException('Data pribadi pegawai tidak ditemukan.');
        }

        $pendidikan = $this->repository->findPendidikanByIdAndPribadiId($id, $pribadi->id);
        if ($pendidikan === null) {
            throw new ModelNotFoundException('Data riwayat pendidikan tidak ditemukan.');
        }

        $data = [];
        if (array_key_exists('jenjang', $payload))      $data['jenjang'] = $payload['jenjang'];
        if (array_key_exists('institusi', $payload))    $data['institusi'] = $payload['institusi'];
        if (array_key_exists('jurusan', $payload))      $data['jurusan'] = $payload['jurusan'];
        if (array_key_exists('tahun_lulus', $payload))  $data['tahun_lulus'] = (int) $payload['tahun_lulus'];
        if (array_key_exists('nomor_ijazah', $payload)) $data['nomor_ijazah'] = $payload['nomor_ijazah'];

        if ($file !== null) {
            $data['ijazah_file_path'] = $this->handleFileUpload('dokumen/ijazah', 'ijazah', $pegawaiId, $file, $pendidikan->ijazah_file_path);
        }

        $updated = $this->repository->updatePendidikan($pendidikan, $data);

        return $this->formatPendidikan($updated);
    }

    public function deletePendidikan(int $id, int $pegawaiId): array
    {
        $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            throw new ModelNotFoundException('Data pribadi pegawai tidak ditemukan.');
        }

        $pendidikan = $this->repository->findPendidikanByIdAndPribadiId($id, $pribadi->id);
        if ($pendidikan === null) {
            throw new ModelNotFoundException('Data riwayat pendidikan tidak ditemukan.');
        }

        if ($pendidikan->ijazah_file_path && file_exists(public_path($pendidikan->ijazah_file_path))) {
            @unlink(public_path($pendidikan->ijazah_file_path));
        }

        $this->repository->deletePendidikan($pendidikan);

        return ['id' => $id];
    }

    private function formatPendidikan($item): array
    {
        return [
            'id'           => $item->id,
            'jenjang'      => $item->jenjang,
            'institusi'    => $item->institusi,
            'jurusan'      => $item->jurusan,
            'tahun_lulus'  => $item->tahun_lulus,
            'nomor_ijazah' => $item->nomor_ijazah,
            'link_ijazah'  => $item->ijazah_file_path ? '/'.$item->ijazah_file_path : null,
        ];
    }
}
