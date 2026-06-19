<?php

namespace App\Services\Hrd;

use App\Repositories\Hrd\HrdKeluargaRepository;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class HrdKeluargaService
{
    public function __construct(private readonly HrdKeluargaRepository $repository) {}

    // ── Pasangan ──────────────────────────────────────────────────────────────

    public function getAllPasangan(int $pegawaiId): array
    {
        $items = $this->repository->getPasanganByPegawaiId($pegawaiId)
            ->map(fn ($p) => array_merge($p->toArray(), [
                'link_buku_nikah' => $p->buku_nikah_file_path ? '/'.$p->buku_nikah_file_path : null,
            ]))->values()->toArray();

        return ['label' => 'Data Pasangan', 'total' => count($items), 'items' => $items];
    }

    public function createPasangan(int $pegawaiId, array $data, ?UploadedFile $file = null): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);

        if ($file !== null) {
            $fileName = uniqid('buku_nikah_').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('dokumen/pasangan'), $fileName);
            $data['buku_nikah_file_path'] = 'dokumen/pasangan/'.$fileName;
        }

        $data['pegawai_pribadi_id'] = $pribadi->id;
        $data['status_tanggungan'] = filter_var($data['status_tanggungan'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $result = $this->repository->createPasangan($data);

        return ['id' => $result->id, 'nama_lengkap' => $result->nama_lengkap];
    }

    public function updatePasangan(int $id, int $pegawaiId, array $data, ?UploadedFile $file = null): array
    {
        $pasangan = $this->repository->findPasanganByIdAndPegawaiId($id, $pegawaiId);
        if ($pasangan === null) {
            throw new InvalidArgumentException('Data pasangan tidak ditemukan.');
        }

        if ($file !== null) {
            if ($pasangan->buku_nikah_file_path && file_exists(public_path($pasangan->buku_nikah_file_path))) {
                @unlink(public_path($pasangan->buku_nikah_file_path));
            }
            $fileName = uniqid('buku_nikah_').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('dokumen/pasangan'), $fileName);
            $data['buku_nikah_file_path'] = 'dokumen/pasangan/'.$fileName;
        }

        if (array_key_exists('status_tanggungan', $data)) {
            $data['status_tanggungan'] = filter_var($data['status_tanggungan'], FILTER_VALIDATE_BOOLEAN);
        }

        $updated = $this->repository->updatePasangan($pasangan, $data);

        return ['id' => $updated->id, 'nama_lengkap' => $updated->nama_lengkap];
    }

    public function deletePasangan(int $id, int $pegawaiId): array
    {
        $pasangan = $this->repository->findPasanganByIdAndPegawaiId($id, $pegawaiId);
        if ($pasangan === null) {
            throw new InvalidArgumentException('Data pasangan tidak ditemukan.');
        }

        if ($pasangan->buku_nikah_file_path && file_exists(public_path($pasangan->buku_nikah_file_path))) {
            @unlink(public_path($pasangan->buku_nikah_file_path));
        }

        $this->repository->deletePasangan($pasangan);

        return ['id' => $id];
    }

    // ── Anak ──────────────────────────────────────────────────────────────────

    public function getAllAnak(int $pegawaiId): array
    {
        $items = $this->repository->getAnakByPegawaiId($pegawaiId)
            ->map(fn ($a) => array_merge($a->toArray(), [
                'link_akta_kelahiran' => $a->akta_kelahiran_file_path ? '/'.$a->akta_kelahiran_file_path : null,
            ]))->values()->toArray();

        return ['label' => 'Data Anak', 'total' => count($items), 'items' => $items];
    }

    public function createAnak(int $pegawaiId, array $data, ?UploadedFile $file = null): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);

        if ($file !== null) {
            $fileName = uniqid('akta_kelahiran_').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('dokumen/anak'), $fileName);
            $data['akta_kelahiran_file_path'] = 'dokumen/anak/'.$fileName;
        }

        $data['pegawai_pribadi_id'] = $pribadi->id;
        $data['status_tanggungan'] = filter_var($data['status_tanggungan'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $result = $this->repository->createAnak($data);

        return ['id' => $result->id, 'nama_lengkap' => $result->nama_lengkap];
    }

    public function updateAnak(int $id, int $pegawaiId, array $data, ?UploadedFile $file = null): array
    {
        $anak = $this->repository->findAnakByIdAndPegawaiId($id, $pegawaiId);
        if ($anak === null) {
            throw new InvalidArgumentException('Data anak tidak ditemukan.');
        }

        if ($file !== null) {
            if ($anak->akta_kelahiran_file_path && file_exists(public_path($anak->akta_kelahiran_file_path))) {
                @unlink(public_path($anak->akta_kelahiran_file_path));
            }
            $fileName = uniqid('akta_kelahiran_').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('dokumen/anak'), $fileName);
            $data['akta_kelahiran_file_path'] = 'dokumen/anak/'.$fileName;
        }

        if (array_key_exists('status_tanggungan', $data)) {
            $data['status_tanggungan'] = filter_var($data['status_tanggungan'], FILTER_VALIDATE_BOOLEAN);
        }

        $updated = $this->repository->updateAnak($anak, $data);

        return ['id' => $updated->id, 'nama_lengkap' => $updated->nama_lengkap];
    }

    public function deleteAnak(int $id, int $pegawaiId): array
    {
        $anak = $this->repository->findAnakByIdAndPegawaiId($id, $pegawaiId);
        if ($anak === null) {
            throw new InvalidArgumentException('Data anak tidak ditemukan.');
        }

        if ($anak->akta_kelahiran_file_path && file_exists(public_path($anak->akta_kelahiran_file_path))) {
            @unlink(public_path($anak->akta_kelahiran_file_path));
        }

        $this->repository->deleteAnak($anak);

        return ['id' => $id];
    }

    // ── Orang Tua ─────────────────────────────────────────────────────────────

    public function getAllOrangTua(int $pegawaiId): array
    {
        $items = $this->repository->getOrangTuaByPegawaiId($pegawaiId)
            ->map(fn ($o) => $o->toArray())->values()->toArray();

        return ['label' => 'Data Orang Tua', 'total' => count($items), 'items' => $items];
    }

    public function createOrangTua(int $pegawaiId, array $data): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);
        $data['pegawai_pribadi_id'] = $pribadi->id;

        $result = $this->repository->createOrangTua($data);

        return ['id' => $result->id, 'nama_ayah' => $result->nama_ayah, 'nama_ibu' => $result->nama_ibu];
    }

    public function updateOrangTua(int $id, int $pegawaiId, array $data): array
    {
        $orangTua = $this->repository->findOrangTuaByIdAndPegawaiId($id, $pegawaiId);
        if ($orangTua === null) {
            throw new InvalidArgumentException('Data orang tua tidak ditemukan.');
        }

        $updated = $this->repository->updateOrangTua($orangTua, $data);

        return ['id' => $updated->id, 'nama_ayah' => $updated->nama_ayah, 'nama_ibu' => $updated->nama_ibu];
    }

    public function deleteOrangTua(int $id, int $pegawaiId): array
    {
        $orangTua = $this->repository->findOrangTuaByIdAndPegawaiId($id, $pegawaiId);
        if ($orangTua === null) {
            throw new InvalidArgumentException('Data orang tua tidak ditemukan.');
        }

        $this->repository->deleteOrangTua($orangTua);

        return ['id' => $id];
    }

    // ── Kontak Darurat ────────────────────────────────────────────────────────

    public function getAllKontakDarurat(int $pegawaiId): array
    {
        $items = $this->repository->getKontakDaruratByPegawaiId($pegawaiId)
            ->map(fn ($k) => $k->toArray())->values()->toArray();

        return ['label' => 'Data Kontak Darurat', 'total' => count($items), 'items' => $items];
    }

    public function createKontakDarurat(int $pegawaiId, array $data): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);
        $data['pegawai_pribadi_id'] = $pribadi->id;

        $result = $this->repository->createKontakDarurat($data);

        return ['id' => $result->id, 'nama_kontak' => $result->nama_kontak];
    }

    public function updateKontakDarurat(int $id, int $pegawaiId, array $data): array
    {
        $kontakDarurat = $this->repository->findKontakDaruratByIdAndPegawaiId($id, $pegawaiId);
        if ($kontakDarurat === null) {
            throw new InvalidArgumentException('Data kontak darurat tidak ditemukan.');
        }

        $updated = $this->repository->updateKontakDarurat($kontakDarurat, $data);

        return ['id' => $updated->id, 'nama_kontak' => $updated->nama_kontak];
    }

    public function deleteKontakDarurat(int $id, int $pegawaiId): array
    {
        $kontakDarurat = $this->repository->findKontakDaruratByIdAndPegawaiId($id, $pegawaiId);
        if ($kontakDarurat === null) {
            throw new InvalidArgumentException('Data kontak darurat tidak ditemukan.');
        }

        $this->repository->deleteKontakDarurat($kontakDarurat);

        return ['id' => $id];
    }

    // ── Tanggungan Lain ───────────────────────────────────────────────────────

    public function getAllTanggunganLain(int $pegawaiId): array
    {
        $items = $this->repository->getTanggunganLainByPegawaiId($pegawaiId)
            ->map(fn ($t) => $t->toArray())->values()->toArray();

        return ['label' => 'Data Tanggungan Lain', 'total' => count($items), 'items' => $items];
    }

    public function createTanggunganLain(int $pegawaiId, array $data): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);
        $data['pegawai_pribadi_id'] = $pribadi->id;

        if (array_key_exists('status_tanggungan', $data)) {
            $data['status_tanggungan'] = filter_var($data['status_tanggungan'], FILTER_VALIDATE_BOOLEAN);
        }

        $result = $this->repository->createTanggunganLain($data);

        return ['id' => $result->id, 'nama' => $result->nama];
    }

    public function updateTanggunganLain(int $id, int $pegawaiId, array $data): array
    {
        $tanggungan = $this->repository->findTanggunganLainByIdAndPegawaiId($id, $pegawaiId);
        if ($tanggungan === null) {
            throw new InvalidArgumentException('Data tanggungan lain tidak ditemukan.');
        }

        if (array_key_exists('status_tanggungan', $data)) {
            $data['status_tanggungan'] = filter_var($data['status_tanggungan'], FILTER_VALIDATE_BOOLEAN);
        }

        $updated = $this->repository->updateTanggunganLain($tanggungan, $data);

        return ['id' => $updated->id, 'nama' => $updated->nama];
    }

    public function deleteTanggunganLain(int $id, int $pegawaiId): array
    {
        $tanggungan = $this->repository->findTanggunganLainByIdAndPegawaiId($id, $pegawaiId);
        if ($tanggungan === null) {
            throw new InvalidArgumentException('Data tanggungan lain tidak ditemukan.');
        }

        $this->repository->deleteTanggunganLain($tanggungan);

        return ['id' => $id];
    }
}
