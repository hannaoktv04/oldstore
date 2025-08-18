<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use App\Models\User;
USE App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('peri::admin.users.index', compact('users', 'roles'));
    }

    public function assignRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'Required|exists:roles,id',
            'status' => 'required|boolean'
        ]);
        $user->roles()->sync($request->input('roles', []));

        $user->active = $request->input('status');
        $user->save();

        return back()->with('success', 'User berhasil diperbarui');
    }
}
