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
        'nama',
        'unit_kerja_id',
        'tmt_mulai',
        'tmt_selesai',
        'sk_file_id',
    ];

    protected function casts(): array
    {
        return [
            'tmt_mulai' => 'date',
            'tmt_selesai' => 'date',
        ];
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function skFile()
    {
        return $this->belongsTo(FileModel::class, 'sk_file_id');
    }

    public function pegawaiPekerjaan()
    {
        return $this->hasMany(PegawaiPekerjaan::class);
    }

    public function riwayatPekerjaan()
    {
        return $this->hasMany(RiwayatPekerjaan::class);
    }
}
