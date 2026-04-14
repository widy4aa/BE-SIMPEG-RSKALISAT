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
        'jenis_pegawai_id',
        'profesi_id',
        'jabatan_id',
        'status_pegawai',
        'tgl_masuk',
        'pangkat_id',
        'golongan_ruang_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pribadi()
    {
        return $this->hasOne(PegawaiPribadi::class);
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

    public function golonganRuang()
    {
        return $this->belongsTo(GolonganRuang::class);
    }

    public function keluarga()
    {
        return $this->hasManyThrough(Keluarga::class, PegawaiPribadi::class, 'pegawai_id', 'pegawai_pribadi_id', 'id', 'id');
    }

    public function jadwalDiklat()
    {
        return $this->hasMany(ListJadwalDiklat::class);
    }

    public function profesiPegawai()
    {
        return $this->hasMany(ProfesiPegawai::class);
    }

    public function pangkatPegawai()
    {
        return $this->hasMany(PangkatPegawai::class);
    }

    public function jabatanPegawai()
    {
        return $this->hasMany(JabatanPegawai::class);
    }

    public function golonganRuangPegawai()
    {
        return $this->hasMany(GolonganRuangPegawai::class);
    }

    public function unitKerjaPegawai()
    {
        return $this->hasMany(UnitKerjaPegawai::class);
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
