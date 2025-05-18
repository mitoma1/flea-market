<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * 登録フォームを表示
     */
    public function show()
    {
        return view('auth.register'); // 'auth.register' でビューを呼び出し
    }

    /**
     * 登録処理
     */
    public function register(RegisterRequest $request)
    {
        // ユーザー作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 自動ログイン
        Auth::login($user);

        // ログイン後のリダイレクト先（例：ホーム）
        return redirect()->route('profile.setup');
    }
}
