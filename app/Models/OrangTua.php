<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrangTua extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orang_tua';

    protected $fillable = [
        'pegawai_pribadi_id',
        'nama_ayah',
        'nama_ibu',
        'status_hidup',
        'alamat',
    ];

    public function pegawaiPribadi()
    {
        return $this->belongsTo(PegawaiPribadi::class);
    }
}
