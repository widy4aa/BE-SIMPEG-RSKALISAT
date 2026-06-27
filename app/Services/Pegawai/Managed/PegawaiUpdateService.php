<?php

namespace App\Services\Pegawai\Managed;

use App\Repositories\Pegawai\Managed\PegawaiRepository;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class PegawaiUpdateService
{
    public function __construct(private readonly PegawaiRepository $repository) {}

    public function updateInti(int $pegawaiId, array $data): array
    {
        $pegawai = $this->repository->findPegawaiById($pegawaiId);

        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        if (isset($data['tgl_masuk']) && $data['tgl_masuk'] !== null && $data['tgl_masuk'] !== '') {
            if (\Carbon\Carbon::parse((string) $data['tgl_masuk'])->startOfDay()->isAfter(\Carbon\Carbon::today())) {
                throw new InvalidArgumentException('tgl_masuk tidak boleh melebihi tanggal sekarang');
            }
        }

        $updated = $this->repository->updatePegawaiInti($pegawai, $data);

        return [
            'id_pegawai'     => $updated->id,
            'nik'            => $updated->nik,
            'nip'            => $updated->nip,
            'nama'           => $updated->nama,
            'status_pegawai' => $updated->status_pegawai,
        ];
    }

    public function updatePribadi(
        int $pegawaiId,
        array $data,
        ?UploadedFile $fotoFile = null,
        ?UploadedFile $ktpFile = null,
        ?UploadedFile $kkFile = null
    ): array {
        $pegawai = $this->repository->findPegawaiById($pegawaiId);

        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);

        if ($fotoFile !== null) {
            $folder = public_path('dokumen/foto-profil');
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            if ($pribadi->foto_path && file_exists(public_path($pribadi->foto_path))) {
                @unlink(public_path($pribadi->foto_path));
            }
            $filename = sprintf('foto-%d-%d.%s', $pegawaiId, time(), $fotoFile->getClientOriginalExtension());
            $fotoFile->move($folder, $filename);
            $data['foto_path'] = 'dokumen/foto-profil/'.$filename;
        }

        if ($ktpFile !== null) {
            $folder = public_path('dokumen/ktp');
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            if ($pribadi->ktp_file_path && file_exists(public_path($pribadi->ktp_file_path))) {
                @unlink(public_path($pribadi->ktp_file_path));
            }
            $filename = sprintf('ktp-%d-%d.%s', $pegawaiId, time(), $ktpFile->getClientOriginalExtension());
            $ktpFile->move($folder, $filename);
            $data['ktp_file_path'] = 'dokumen/ktp/'.$filename;
        }

        if ($kkFile !== null) {
            $folder = public_path('dokumen/kk');
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            if ($pribadi->kk_file_path && file_exists(public_path($pribadi->kk_file_path))) {
                @unlink(public_path($pribadi->kk_file_path));
            }
            $filename = sprintf('kk-%d-%d.%s', $pegawaiId, time(), $kkFile->getClientOriginalExtension());
            $kkFile->move($folder, $filename);
            $data['kk_file_path'] = 'dokumen/kk/'.$filename;
        }

        unset($data['foto_profil'], $data['ktp_file'], $data['kk_file']);

        $updated = $this->repository->updatePegawaiPribadi($pribadi, $data);

        return [
            'id_pegawai'          => $pegawaiId,
            'jenis_kelamin'       => $updated->jenis_kelamin,
            'tanggal_lahir'       => $updated->tanggal_lahir?->format('Y-m-d'),
            'agama'               => $updated->agama,
            'status_perkawinan'   => $updated->status_perkawinan,
            'alamat'              => $updated->alamat,
            'no_telp'             => $updated->no_telp,
            'pendidikan_terakhir' => $updated->pendidikan_terakhir,
            'no_kk'               => $updated->no_kk,
            'link_foto'           => $updated->foto_path ? '/'.$updated->foto_path : null,
            'link_ktp'            => $updated->ktp_file_path ? '/'.$updated->ktp_file_path : null,
            'link_kk'             => $updated->kk_file_path ? '/'.$updated->kk_file_path : null,
        ];
    }
}
