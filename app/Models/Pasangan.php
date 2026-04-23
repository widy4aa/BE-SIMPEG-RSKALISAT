<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pasangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pasangan';

    protected $fillable = [
        'pegawai_pribadi_id',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'pekerjaan',
        'instansi',
        'status_pernikahan',
        'tanggal_pernikahan',
        'nomor_buku_nikah',
        'status_tanggungan',
        'npwp_pasangan',
        'buku_nikah_file_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_pernikahan' => 'date',
            'status_tanggungan' => 'boolean',
        ];
    }

    public function pegawaiPribadi()
    {
        return $this->belongsTo(PegawaiPribadi::class);
    }
}
