<?php

namespace App\Http\Controllers\rt\peri;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman pengaturan profil (Blade)
     */
    public function edit()
    {
        // Mengarahkan ke file view user/setting.blade.php
        return view('peri::user.setting'); 
    }

    /**
     * Memperbarui data profil lengkap
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        assert($user instanceof User);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_telp'  => 'nullable|string|max:20',
            'alamat'   => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        $user->nama = $request->nama;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->no_telp = $request->no_telp;
        $user->alamat = $request->alamat;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil Anda telah berhasil diperbarui!');
    }
}