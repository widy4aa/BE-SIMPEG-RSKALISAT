<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pangkat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pangkat';

    protected $fillable = [
        'nama',
        'pejabat_penetap',
        'tmt_sk',
        'sk_file_path',
    ];

    protected function casts(): array
    {
        return [
            'tmt_sk' => 'date',
        ];
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function pangkatPegawai()
    {
        return $this->hasMany(PangkatPegawai::class);
    }
}
