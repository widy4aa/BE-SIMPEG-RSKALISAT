<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPerubahanData extends Model
{
    use HasFactory;

    protected $table = 'detail_perubahan_data';

    protected $fillable = [
        'id_perubahan_data',
        'target_table',
        'kolom',
        'value',
        'old_value',
    ];

    public function perubahanData()
    {
        return $this->belongsTo(PerubahanData::class, 'id_perubahan_data');
    }
}
