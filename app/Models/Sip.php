<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sip extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sip';

    protected $fillable = [
        'pegawai_pekerjaan_id',
        'jenis_sip_id',
        'nomor_sip',
        'tanggal_terbit',
        'tanggal_kadaluarsa',
        'sk_file_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'tanggal_kadaluarsa' => 'date',
        ];
    }

    public function pegawaiPekerjaan()
    {
        return $this->belongsTo(PegawaiPekerjaan::class);
    }

    public function jenisSip()
    {
        return $this->belongsTo(JenisSip::class);
    }

    public function skFile()
    {
        return $this->belongsTo(FileModel::class, 'sk_file_id');
    }
}
