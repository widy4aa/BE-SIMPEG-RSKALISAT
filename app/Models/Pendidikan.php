<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendidikan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendidikan';

    protected $fillable = [
        'pegawai_pribadi_id',
        'jenjang',
        'institusi',
        'jurusan',
        'tahun_lulus',
        'nomor_ijazah',
        'ijazah_file_path',
    ];

    public function pegawaiPribadi()
    {
        return $this->belongsTo(PegawaiPribadi::class);
    }
}
