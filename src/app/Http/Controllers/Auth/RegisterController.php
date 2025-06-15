<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Registered;
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
        return view('auth.register');
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

        // メール認証メール送信（ここが重要！）
        event(new Registered($user));

        // 自動ログイン
        Auth::login($user);

        // メール認証画面にリダイレクト
        return redirect()->route('verification.notice');
    }
}
