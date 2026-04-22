<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jabatan';

    protected $fillable = [
        'unit_kerja_id',
        'nama',
        'tmt_mulai',
        'tmt_selesai',
        'sk_file_path',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    protected function casts(): array
    {
        return [
            'tmt_mulai' => 'date',
            'tmt_selesai' => 'date',
        ];
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function jabatanPegawai()
    {
        return $this->hasMany(JabatanPegawai::class);
    }
}
