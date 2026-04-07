<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatPekerjaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'riwayat_pekerjaan';

    protected $fillable = [
        'pegawai_id',
        'pegawai_pekerjaan_id',
        'jabatan_id',
        'pangkat_id',
        'is_current',
        'started_at',
        'ended_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'started_at' => 'date',
            'ended_at' => 'date',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pegawaiPekerjaan()
    {
        return $this->belongsTo(PegawaiPekerjaan::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class);
    }
}
