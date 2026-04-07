<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'nik',
        'nip',
        'nama',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pribadi()
    {
        return $this->hasOne(PegawaiPribadi::class);
    }

    public function pekerjaan()
    {
        return $this->hasMany(PegawaiPekerjaan::class);
    }

    public function riwayatPekerjaan()
    {
        return $this->hasMany(RiwayatPekerjaan::class);
    }

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class);
    }

    public function jadwalDiklat()
    {
        return $this->hasMany(ListJadwalDiklat::class);
    }
}
