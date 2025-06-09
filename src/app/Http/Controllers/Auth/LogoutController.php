<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        auth()->logout(); // ユーザーのログアウト
        $request->session()->invalidate(); // セッションを無効にする
        $request->session()->regenerateToken(); // CSRFトークンを再生成

        // 🔽 ログイン画面へリダイレクト（login.blade.phpに対応するルート）
        return redirect()->route('login');
    }
}
