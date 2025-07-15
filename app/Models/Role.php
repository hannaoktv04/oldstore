<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Role extends Model
{
    protected $fillable = ['nama_role'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_role');
    }

    public function hasRole($roleName)
    {
        if ($roleName === 'admin') {
            $allowedUsernames = collect(['giantmountain', 'hannaoktv']);

            return $this->roles->pluck('nama_role')->contains('admin') &&
                $allowedUsernames->contains($this->username);
        }

        return $this->roles->pluck('nama_role')->contains($roleName);
    }

}
