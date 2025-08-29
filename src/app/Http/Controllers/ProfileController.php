<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;

class ProfileController extends Controller
{
    /**
     * マイページ（プロフィール + 商品一覧）
     */
    public function show()
    {
        $user = Auth::user();
        $userId = $user->id;

        // -------------------------------
        // 出品商品（取引がまだ完了していないもの）
        // -------------------------------
        $sellingProducts = $user->products()
            ->whereHas('trade', function ($q) {
                $q->where('status', '!=', 'completed');
            }, '<', 1) // tradeが無いものも含める
            ->latest()
            ->get();

        // -------------------------------
        // 購入商品（取引完了済み）
        // -------------------------------
        $purchasedProducts = Product::whereHas('trade', function ($q) use ($userId) {
            $q->where('buyer_id', $userId)
                ->where('status', 'completed');
        })->latest()->get();

        // -------------------------------
        // 取引中商品（自分が出品 or 購入している進行中の取引）
        // -------------------------------
        $tradingProducts = Product::whereHas('trade', function ($q) use ($userId) {
            $q->where('status', 'in_progress')
                ->where(function ($q2) use ($userId) {
                    $q2->where('buyer_id', $userId)
                        ->orWhereHas('product', fn($q3) => $q3->where('user_id', $userId));
                });
        })
            ->with(['trade.messages' => fn($q) => $q->orderBy('created_at', 'asc')])
            ->get()
            ->map(function ($product) use ($userId) {
                $product->unread_messages_count = optional($product->trade)
                    ->messages
                    ->where('user_id', '!=', $userId)
                    ->where('is_read', false)
                    ->count();
                return $product;
            })
            ->sortByDesc(fn($product) => optional($product->trade)->messages->max('created_at') ?? optional($product->trade)->created_at)
            ->values();

        return view('mypage.profile', compact(
            'user',
            'sellingProducts',
            'purchasedProducts',
            'tradingProducts'
        ));
    }

    /**
     * プロフィール編集画面
     */
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile_edit', compact('user'));
    }

    /**
     * プロフィール更新
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username'     => 'required|string|max:255',
            'postal_code'  => 'required|string|max:10',
            'address'      => 'required|string|max:255',
            'building'     => 'nullable|string|max:255',
            'avatar'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // 古い画像削除
            if ($user->profile_image && file_exists(public_path('images/' . basename($user->profile_image)))) {
                unlink(public_path('images/' . basename($user->profile_image)));
            }

            // 新しい画像保存
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $user->profile_image = 'images/' . $filename;
        }

        $user->name     = $request->input('username');
        $user->postcode = $request->input('postal_code');
        $user->address  = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()->route('mypage.profile.show')->with('success', 'プロフィールを更新しました');
    }
}
