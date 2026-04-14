<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GolonganRuang extends Model
{
    use HasFactory;

    protected $table = 'golongan_ruang';

    protected $fillable = [
        'nama',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function golonganRuangPegawai()
    {
        return $this->hasMany(GolonganRuangPegawai::class);
    }
}
