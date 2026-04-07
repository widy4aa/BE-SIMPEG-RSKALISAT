<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSip extends Model
{
    use HasFactory;

    protected $table = 'jenis_sip';

    protected $fillable = [
        'nama',
    ];

    public function sips()
    {
        return $this->hasMany(Sip::class);
    }
}
