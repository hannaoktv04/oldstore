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
        'username',
        'nama',
        'email',
        'no_telp',
        'alamat',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Auto hash password 
     */
    public function setPasswordAttribute($password)
    {
        if ($password) {
            $this->attributes['password'] = Str::startsWith($password, '$2y$')
                ? $password
                : Hash::make($password);
        }
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }
}
