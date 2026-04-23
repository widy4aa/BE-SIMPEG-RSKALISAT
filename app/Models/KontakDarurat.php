<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KontakDarurat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kontak_darurat';

    protected $fillable = [
        'pegawai_pribadi_id',
        'nama_kontak',
        'hubungan_keluarga',
        'nomor_hp',
        'alamat',
    ];

    public function pegawaiPribadi()
    {
        return $this->belongsTo(PegawaiPribadi::class);
    }
}
