<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect()->route('home');
    }
}