<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TanggunganLain extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tanggungan_lain';

    protected $fillable = [
        'pegawai_pribadi_id',
        'nama',
        'hubungan_keluarga',
        'status_tanggungan',
    ];

    public function pegawaiPribadi()
    {
        return $this->belongsTo(PegawaiPribadi::class);
    }
}
