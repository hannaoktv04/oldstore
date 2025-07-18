<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'nama', 'nip', 'jabatan', 'email', 'password',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Str::startsWith($password, '$2y$')
            ? $password
            : Hash::make($password);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_role');
    }

    public function hasRole($roleName)
    {
        return $this->roles->pluck('nama_role')->contains($roleName);
    }
}
