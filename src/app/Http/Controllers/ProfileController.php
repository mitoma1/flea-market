<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProfileController extends Controller
{
    /**
     * プロフィール表示（マイページ）
     */
    public function show()
    {
        $user = Auth::user();

        if (!$user instanceof \App\Models\User) {
            abort(500, 'Authenticated user is not a valid User model instance.');
        }

        // 出品した商品
        $sellingProducts = Product::where('user_id', $user->id)
            ->latest()
            ->get();

        // ✅ 購入した商品（リレーションから取得）
        $purchasedProducts = $user->purchasedProducts()->latest()->get();

        return view('mypage.profile', compact('user', 'sellingProducts', 'purchasedProducts'));
    }
    /**
     * プロフィール編集画面表示
     */
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile_edit', compact('user'));
    }

    /**
     * プロフィール更新処理
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // バリデーション
        $request->validate([
            'username'     => 'required|string|max:255',
            'postal_code'  => 'required|string|max:10',
            'address'      => 'required|string|max:255',
            'building'     => 'nullable|string|max:255',
            'avatar'       => 'nullable|image|max:2048',
        ]);

        // 画像がアップロードされた場合
        if ($request->hasFile('avatar')) {
            // 既存画像削除
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // 新しい画像保存
            $path = $request->file('avatar')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // プロフィール情報更新
        $user->name     = $request->input('username');
        $user->postcode = $request->input('postal_code');
        $user->address  = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()->route('products.index')->with('success', 'プロフィールを更新しました');
    }
}
