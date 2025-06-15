<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MypageController extends Controller
{
    /**
     * マイページトップ
     */
    public function index()
    {
        return view('mypage.index'); // resources/views/mypage/index.blade.php
    }

    /**
     * ユーザープロフィール＆出品商品一覧表示
     */
    public function profile()
    {
        // ログイン中ユーザーを取得
        $user = auth()->user();

        // ログインしていなければログイン画面へリダイレクト
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        // 出品した商品を最新順で取得
        $sellingProducts = $user->products()->latest()->get();

        // （購入商品があれば同様に取得。なければ省略可）
        // $purchasedProducts = $user->purchasedProducts()->latest()->get();

        // ビューにデータを渡す
        return view('mypage.profile', compact('user', 'sellingProducts'));
    }
}
