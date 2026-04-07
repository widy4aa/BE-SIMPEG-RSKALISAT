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
        'sk_file_id',
    ];

    protected function casts(): array
    {
        return [
            'tmt_sk' => 'date',
        ];
    }

    public function skFile()
    {
        return $this->belongsTo(FileModel::class, 'sk_file_id');
    }

    public function pegawaiPekerjaan()
    {
        return $this->hasMany(PegawaiPekerjaan::class);
    }

    public function riwayatPekerjaan()
    {
        return $this->hasMany(RiwayatPekerjaan::class);
    }
}
