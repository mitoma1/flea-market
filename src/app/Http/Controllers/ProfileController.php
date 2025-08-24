<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Trade;

class ProfileController extends Controller
{
    /**
     * マイページ（プロフィール + 商品一覧）
     */
    public function show()
    {
        $user = Auth::user();

        // 出品商品（取引がまだ完了していないもの）
        $sellingProducts = $user->products()
            ->whereDoesntHave('trade', function ($q) {
                $q->where('status', 'completed');
            })
            ->latest()
            ->get();

        // 購入商品（取引完了かつ両者評価済み）
        $purchasedProducts = $user->purchasedProducts()
            ->whereHas('trade', function ($q) {
                $q->where('status', 'completed')
                    ->where('buyer_completed', true)
                    ->where('seller_completed', true);
            })
            ->latest()
            ->get();

        // 取引中商品（購入者・出品者どちらでも関わる進行中の取引）
        $tradingProducts = Product::whereHas('trade', function ($q) use ($user) {
            $q->where('status', 'in_progress')
                ->where(function ($q2) use ($user) {
                    $q2->where('buyer_id', $user->id)
                        ->orWhereHas('product', fn($q3) => $q3->where('user_id', $user->id));
                });
        })
            ->with(['trade.messages' => fn($q) => $q->orderBy('created_at', 'asc')])
            ->get()
            ->map(function ($product) use ($user) {
                $product->unread_messages_count = optional($product->trade)
                    ->messages
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
                return $product;
            })
            ->sortByDesc(
                fn($product) => optional($product->trade)
                    ->messages
                    ->max('created_at') ?? optional($product->trade)->created_at
            )
            ->values();

        // 平均評価
        $ratings = $user->receivedRatings ?? collect();
        $averageRating = round($ratings->avg('rating') ?? 0);
        $ratingCount = $ratings->count();

        return view('mypage.profile', compact(
            'user',
            'sellingProducts',
            'purchasedProducts',
            'tradingProducts',
            'averageRating',
            'ratingCount'
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
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $user->profile_image = $request->file('avatar')->store('profile_images', 'public');
        }

        $user->name     = $request->input('username');
        $user->postcode = $request->input('postal_code');
        $user->address  = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()->route('mypage.profile.show')->with('success', 'プロフィールを更新しました');
    }
}
