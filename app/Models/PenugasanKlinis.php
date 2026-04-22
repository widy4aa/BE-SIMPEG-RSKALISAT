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
        'pegawai_id',
        'nomor_surat',
        'tgl_mulai',
        'tgl_kadaluarsa',
        'is_current',
        'dokumen_file_path',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_kadaluarsa' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
