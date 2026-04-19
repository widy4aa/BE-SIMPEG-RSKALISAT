<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisBiaya extends Model
{
    use HasFactory;

    protected $table = 'jenis_biaya';

    protected $fillable = [
        'nama',
    ];

    public function diklat()
    {
        return $this->hasMany(Diklat::class);
    }
}
