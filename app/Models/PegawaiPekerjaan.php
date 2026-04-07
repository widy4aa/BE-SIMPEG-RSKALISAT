<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PegawaiPekerjaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai_pekerjaan';

    protected $fillable = [
        'pegawai_id',
        'jenis_pegawai_id',
        'profesi_id',
        'jabatan_id',
        'status_pegawai',
        'tgl_masuk',
        'pangkat_id',
        'golongan_ruang',
        'tmt_cpns',
        'tmt_pns',
        'tmt_pangkat_akhir',
        'masa_kerja',
    ];

    protected function casts(): array
    {
        return [
            'tgl_masuk' => 'date',
            'tmt_cpns' => 'date',
            'tmt_pns' => 'date',
            'tmt_pangkat_akhir' => 'date',
            'masa_kerja' => 'date',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisPegawai()
    {
        return $this->belongsTo(JenisPegawai::class);
    }

    public function profesi()
    {
        return $this->belongsTo(Profesi::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class);
    }

    public function riwayatPekerjaan()
    {
        return $this->hasMany(RiwayatPekerjaan::class);
    }

    public function strs()
    {
        return $this->hasMany(StrPegawai::class);
    }

    public function sips()
    {
        return $this->hasMany(Sip::class);
    }

    public function penugasanKlinis()
    {
        return $this->hasMany(PenugasanKlinis::class);
    }
}
