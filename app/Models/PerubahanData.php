<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerubahanData extends Model
{
    use HasFactory;

    protected $table = 'perubahan_data';

    protected $fillable = [
        'user_id',
        'table_name',
        'record_id',
        'field_name',
        'old_value',
        'new_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
