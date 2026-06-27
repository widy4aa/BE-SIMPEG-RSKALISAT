<?php

namespace App\Services\Keluarga\Managed;

use Illuminate\Http\UploadedFile;

abstract class BaseKeluargaService
{
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
