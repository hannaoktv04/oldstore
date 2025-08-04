<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:users',
            'jabatan' => 'required|string|max:100',
            'role' => 'required|exists:roles,nama_role',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $role = Role::where('nama_role', $request->role)->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        Auth::login($user);

        return redirect()->route('portal');
    }
}
