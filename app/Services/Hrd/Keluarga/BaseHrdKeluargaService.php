<?php

namespace App\Services\Hrd\Keluarga;

use App\Repositories\Hrd\HrdKeluargaRepository;
use Illuminate\Http\UploadedFile;

abstract class BaseHrdKeluargaService
{
    public function __construct(protected readonly HrdKeluargaRepository $repository) {}

    protected function storeFile(UploadedFile $file, string $folder, string $prefix): string
    {
        $fileName = uniqid($prefix.'_').'.'.$file->getClientOriginalExtension();
        $file->move(public_path($folder), $fileName);

        return $folder.'/'.$fileName;
    }

    protected function deletePublicFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }

    protected function normalizeBoolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
