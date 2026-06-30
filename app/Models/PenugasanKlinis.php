<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class PenugasanKlinis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penugasan_klinis';

    protected $fillable = [
        'pegawai_id',
        'nomor_surat',
        'tgl_mulai',
        'tgl_kadaluarsa',
        'dokumen_file_path',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_kadaluarsa' => 'date',
        ];
    }

    public function getIsCurrentAttribute(): bool
    {
        $today = Carbon::today();

        return ($this->tgl_mulai === null || $this->tgl_mulai->lessThanOrEqualTo($today))
            && ($this->tgl_kadaluarsa === null || $this->tgl_kadaluarsa->greaterThanOrEqualTo($today));
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
