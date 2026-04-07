<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriDiklat extends Model
{
    use HasFactory;

    protected $table = 'kategori_diklat';

    protected $fillable = [
        'nama',
    ];

    public function diklat()
    {
        return $this->hasMany(Diklat::class);
    }
}
