<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function index()
    {
        return view('mypage.index'); // ビューは resources/views/mypage/index.blade.php を想定
    }
    public function profile()
    {
        $user = auth()->user(); // ログイン中のユーザーを取得
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }
        $products = $user->products()->latest()->get(); // 出品した商品を取得

        return view('mypage.profile', compact('user', 'products'));
    }
}
