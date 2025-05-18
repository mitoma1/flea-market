<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    /**
     * ログインフォームを表示
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(LoginRequest $request)
    {
        // ログイン試行
        if (Auth::attempt($request->only('email', 'password'))) {
            // 認証成功：ログイン後のリダイレクト
            return redirect(RouteServiceProvider::HOME); // 実際のダッシュボードに変更
        }

        // 認証失敗：エラーメッセージを返す
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
        ])->withInput();
    }
}
