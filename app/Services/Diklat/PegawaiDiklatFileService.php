<?php

namespace App\Services\Diklat;

use Illuminate\Http\UploadedFile;

class PegawaiDiklatFileService
{
    public function storeSertifikat(int $pegawaiId, UploadedFile $file): string
    {
        $folder = public_path('dokumen/sertif-diklat');
        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = sprintf(
            'sertif-%d-%d.%s',
            $pegawaiId,
            time(),
            $file->getClientOriginalExtension()
        );

        $file->move($folder, $filename);

        return 'dokumen/sertif-diklat/'.$filename;
    }
}
