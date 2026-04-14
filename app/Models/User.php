<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class);
    }

    public function notifications()
    {
        return $this->hasMany(NotificationModel::class);
    }

    public function perubahanData()
    {
        return $this->hasMany(PerubahanData::class);
    }

    public function logActivities()
    {
        return $this->hasMany(LogActivity::class);
    }

}
