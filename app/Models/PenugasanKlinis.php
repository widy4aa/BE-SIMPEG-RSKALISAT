<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenugasanKlinis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penugasan_klinis';

    protected $fillable = [
        'pegawai_pekerjaan_id',
        'nomor_surat',
        'tgl_mulai',
        'tgl_kadaluarsa',
        'dokumen_file_id',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_kadaluarsa' => 'date',
        ];
    }

    public function pegawaiPekerjaan()
    {
        return $this->belongsTo(PegawaiPekerjaan::class);
    }

    public function dokumenFile()
    {
        return $this->belongsTo(FileModel::class, 'dokumen_file_id');
    }
}
