<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diklat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'diklat';

    protected $fillable = [
        'jenis_diklat_id',
        'kategori_diklat_id',
        'created_by',
        'nama_kegiatan',
        'status_kelayakan',
        'status_validasi',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'tempat',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'waktu' => 'datetime:H:i:s',
        ];
    }

    public function jenisDiklat()
    {
        return $this->belongsTo(JenisDiklat::class);
    }

    public function kategoriDiklat()
    {
        return $this->belongsTo(KategoriDiklat::class);
    }

    public function createdByPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'created_by');
    }

    public function jadwalPeserta()
    {
        return $this->hasMany(ListJadwalDiklat::class);
    }
}
