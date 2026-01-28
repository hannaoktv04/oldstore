<?php

namespace App\Http\Controllers\rt\peri;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user terbaru
     */
    public function index()
    {
        $users = User::latest()->get(); //
        return view('peri::admin.users.index', compact('users')); //
    }

    /**
     * Update Informasi Profil User (Tanpa Status)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'   => 'required|string|max:255', 
            'email'  => 'required|email|unique:users,email,' . $id, 
            'no_telp' => 'nullable|string|max:15', 
            'alamat' => 'nullable|string', 
            'role'    => 'required|in:admin,user', 
        ]);

        $user = User::findOrFail($id); 

        $roleData = $request->role;
        if (auth()->id() == $id) {
            $roleData = $user->role; 
        }

        $user->update([
            'nama'    => $request->nama, 
            'email'   => $request->email, 
            'no_telp' => $request->no_telp, 
            'alamat'  => $request->alamat,
            'role'    => $roleData, 
        ]);

        return redirect()->back()->with('success', 'Data profil anggota berhasil diperbarui.'); //
    }
}