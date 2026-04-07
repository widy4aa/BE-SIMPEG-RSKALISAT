<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PegawaiPribadi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai_pribadi';

    protected $fillable = [
        'pegawai_id',
        'pendidikan_terakhir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'alamat',
        'no_telp',
        'email',
        'foto_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function foto()
    {
        return $this->belongsTo(FileModel::class, 'foto_id');
    }

    public function pendidikan()
    {
        return $this->hasMany(Pendidikan::class);
    }
}
