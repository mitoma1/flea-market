<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function store(Product $product)
    {
        $user = Auth::user();

        // すでに購入済みか確認（任意）
        if ($user->purchasedProducts()->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'この商品はすでに購入済みです');
        }

        // 購入処理
        $user->purchasedProducts()->attach($product->id, [
            'payment' => $product->price,
        ]);

        return redirect()->route('mypage')->with('success', '商品を購入しました');
    }
}
