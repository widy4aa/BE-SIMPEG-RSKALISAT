<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListJadwalDiklat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'list_jadwal_diklat';

    protected $fillable = [
        'diklat_id',
        'pegawai_id',
        'laporan_file_id',
        'uploaded_at',
        'status_diklat',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function diklat()
    {
        return $this->belongsTo(Diklat::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function laporanFile()
    {
        return $this->belongsTo(FileModel::class, 'laporan_file_id');
    }
}
