<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = [
        'username', 'nama', 'nip', 'role', 'jabatan', 'email', 'password',
    ];

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Str::startsWith($password, '$2y$')
            ? $password
            : Hash::make($password);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

}
