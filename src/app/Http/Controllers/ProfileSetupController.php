<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileSetupController extends Controller
{
    public function create()
    {
        return view('profile.setup');
    }

    public function store(Request $request)
    {
        $user = Auth::user(); // Ensure this returns an instance of App\Models\User
        if (!$user instanceof \App\Models\User) {
            abort(500, 'Authenticated user is not a valid User model instance.');
        }

        // バリデーション（簡易）
        $request->validate([
            'name' => 'required|string|max:255',
            'postcode' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        // プロフィール画像があれば保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // ユーザー情報更新
        $user->name = $request->input('name');
        $user->postcode = $request->input('postcode');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()->route('mypage')->with('success', 'プロフィールを更新しました');
    }
}
