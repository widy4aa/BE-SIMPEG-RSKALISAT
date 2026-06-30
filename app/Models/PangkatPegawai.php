<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class PangkatPegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pangkat_pegawai';

    protected $fillable = [
        'pegawai_id',
        'pangkat_id',
        'started_at',
        'ended_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
        ];
    }

    public function getIsCurrentAttribute(): bool
    {
        $today = Carbon::today();

        return ($this->started_at === null || $this->started_at->lessThanOrEqualTo($today))
            && ($this->ended_at === null || $this->ended_at->greaterThanOrEqualTo($today));
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
