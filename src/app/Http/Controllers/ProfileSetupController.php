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
        $user = Auth::user();
        if (!$user instanceof \App\Models\User) {
            abort(500, 'Authenticated user is not a valid User model instance.');
        }

        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'postcode' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        // プロフィール画像の保存
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

        // ✅ 商品一覧画面へ遷移（成功メッセージ付き）
        return redirect()->route('products.index')->with('success', 'プロフィールを更新しました');
    }

    public function show()
    {
        $user = Auth::user();
        $listedProducts = $user->listedProducts ?? collect();
        $purchasedProducts = $user->purchasedProducts ?? collect();
        $products = $listedProducts->merge($purchasedProducts);

        return view('mypage.profile', compact('user', 'listedProducts', 'purchasedProducts', 'products'));
    }
    public function edit()
    {
        $user = Auth::user(); // ログインユーザーを取得
        return view('profile.edit', compact('user'));
    }
}
