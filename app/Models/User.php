<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $fillable = [
        'nama', 'nip', 'role', 'jabatan', 'email', 'password',
    ];

    // Gunakan 'nip' sebagai username
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    // Hash password jika belum ter-hash
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
