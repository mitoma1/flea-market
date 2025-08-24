<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Trade;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function store(Product $product)
    {
        $user = Auth::user();

        // すでに購入済みか確認
        if ($user->purchasedProducts()->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'この商品はすでに購入済みです');
        }

        // 購入処理
        $user->purchasedProducts()->attach($product->id, [
            'payment' => $product->price,
        ]);

        // ✅ 取引レコード作成（なければ作成）
        $trade = Trade::firstOrCreate(
            ['product_id' => $product->id],
            [
                'buyer_id' => $user->id,
                'status'   => 'in_progress',
            ]
        );

        // ✅ 取引チャット画面にリダイレクト
        return redirect()->route('trades.show', $trade->id)
            ->with('success', '商品を購入しました。取引を開始します。');
    }
}
