<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function index()
    {
        return view('mypage.index'); // ビューは resources/views/mypage/index.blade.php を想定
    }
}
