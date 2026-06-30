<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class StrPegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'str';

    protected $fillable = [
        'pegawai_id',
        'nomor_str',
        'tanggal_terbit',
        'tanggal_kadaluarsa',
        'sk_file_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'tanggal_kadaluarsa' => 'date',
        ];
    }

    public function getIsCurrentAttribute(): bool
    {
        $today = Carbon::today();

        return ($this->tanggal_terbit === null || $this->tanggal_terbit->lessThanOrEqualTo($today))
            && ($this->tanggal_kadaluarsa === null || $this->tanggal_kadaluarsa->greaterThanOrEqualTo($today));
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
