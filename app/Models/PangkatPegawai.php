<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PangkatPegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pangkat_pegawai';

    protected $fillable = [
        'pegawai_id',
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

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class);
    }
}
