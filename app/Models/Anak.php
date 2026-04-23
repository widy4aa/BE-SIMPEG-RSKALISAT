<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anak extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anak';

    protected $fillable = [
        'pegawai_pribadi_id',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_anak',
        'pendidikan_terakhir',
        'status_tanggungan',
        'usia',
        'keterangan_disabilitas',
        'akta_kelahiran_file_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'status_tanggungan' => 'boolean',
            'usia' => 'integer',
        ];
    }

    public function pegawaiPribadi()
    {
        return $this->belongsTo(PegawaiPribadi::class);
    }
}
