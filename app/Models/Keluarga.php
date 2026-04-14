<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keluarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'keluarga';

    protected $fillable = [
        'pegawai_pribadi_id',
        'nama',
        'hubungan',
        'tanggal_lahir',
        'pekerjaan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function pegawaiPribadi()
    {
        return $this->belongsTo(PegawaiPribadi::class);
    }
}
