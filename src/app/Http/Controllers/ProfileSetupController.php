<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ Authファサードをインポート

class ProfileSetupController extends Controller
{
    public function create()
    {
        // ✅ ここでログインしているユーザー情報を取得し、ビューに渡す
        $user = Auth::user();

        // ビューファイル名が 'profile/setup.blade.php' なので、'profile.setup' と指定します
        return view('profile.setup', compact('user')); // ✅ compact('user') を追加
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User) {
            abort(500, 'Authenticated user is not a valid User model instance.');
        }

        // バリデーション (ビューのname属性とUserモデルのカラム名に合わせます)
        $request->validate([
            'username' => 'required|string|max:255', // ビューのname="username"
            'postal_code' => 'required|string|max:10', // ビューのname="postal_code"
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048', // ビューのname="avatar"
        ]);

        // プロフィール画像の保存
        if ($request->hasFile('avatar')) { // ビューのname="avatar"
            $path = $request->file('avatar')->store('profile_images', 'public');
            $user->profile_image = $path; // Userモデルのカラム名
        }

        // ユーザー情報更新
        $user->name = $request->input('username'); // Userモデルの'name'カラムにビューの'username'を保存
        $user->postcode = $request->input('postal_code'); // Userモデルの'postcode'カラムにビューの'postal_code'を保存
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        // 商品一覧画面へ遷移（成功メッセージ付き）
        return redirect()->route('products.index')->with('success', 'プロフィールを更新しました');
    }

    // ✅ 重要: このProfileSetupControllerは「初回プロフィール設定」に特化させるべきです。
    // そのため、下記の show() と edit() メソッドは通常、別の
    // より汎用的なProfileController (またはMypageController) に移動することを推奨します。
    // このコントローラーに残すと、役割が重複しコード管理が複雑になる可能性があります。
    // 以下は残すか移動するか検討してください。

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
