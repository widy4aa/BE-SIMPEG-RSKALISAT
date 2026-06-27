<?php

namespace App\Services\Hrd\RiwayatKarir;

use App\Repositories\Hrd\HrdRiwayatKarirRepository;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

abstract class BaseHrdRiwayatKarirService
{
    public function __construct(protected readonly HrdRiwayatKarirRepository $repository) {}

    protected function getPegawaiOrFail(int $pegawaiId)
    {
        $pegawai = $this->repository->findPegawaiById($pegawaiId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        return $pegawai;
    }

    protected function handleFileUpload(string $folder, string $prefix, int $pegawaiId, ?UploadedFile $file, ?string $oldPath): ?string
    {
        if ($file === null) {
            return null;
        }

        $fullFolder = public_path($folder);
        if (! is_dir($fullFolder)) {
            mkdir($fullFolder, 0755, true);
        }

        if ($oldPath && file_exists(public_path($oldPath))) {
            @unlink(public_path($oldPath));
        }

        $filename = sprintf('%s-%d-%d.%s', $prefix, $pegawaiId, time(), $file->getClientOriginalExtension());
        $file->move($fullFolder, $filename);

        return $folder.'/'.$filename;
    }
}
