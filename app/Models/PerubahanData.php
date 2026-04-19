<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerubahanData extends Model
{
    use HasFactory;

    protected $table = 'perubahan_data';

    protected $fillable = [
        'by_user',
        'fitur',
        'status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'by_user');
    }

    public function details()
    {
        return $this->hasMany(DetailPerubahanData::class, 'id_perubahan_data');
    }

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }
}
