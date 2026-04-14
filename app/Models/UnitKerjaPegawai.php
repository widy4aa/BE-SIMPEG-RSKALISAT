<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitKerjaPegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unit_kerja_pegawai';

    protected $fillable = [
        'pegawai_id',
        'unit_kerja_id',
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

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }
}
