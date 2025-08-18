<?php

namespace App\Http\Controllers\rt\peri;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'profile_picture' => [
                'required',
                File::image()->types(['jpg','jpeg','png','webp'])->max(2 * 1024) // 2MB
            ],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        $fullUrl = asset('storage/' . $path);

        $user->profile_picture = $fullUrl;
        $user->save();

        return response()->json([
            'message' => 'Profile picture updated.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'profile_picture' => $user->profile_picture, 
            ]
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_picture' => $user->profile_picture
                ?: asset('images/avatars/jay.jpg'),
        ]);
    }
}
