<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\TradeRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeCompletedMail;

class TradeController extends Controller
{
    /**
     * 取引チャット画面表示
     */
    public function show(Trade $trade)
    {
        $trade->load('product.user', 'buyer', 'messages.user');

        $partner = null;
        if ($trade->product && Auth::id() === $trade->product->user_id) {
            $partner = $trade->buyer;
        } elseif ($trade->product) {
            $partner = $trade->product->user;
        }

        $messages = $trade->messages()->orderBy('created_at')->get();
        $product = $trade->product;

        $alreadyRated = TradeRating::where('product_id', $product->id)
            ->where('rater_user_id', Auth::id())
            ->exists();

        $sidebarTrades = Trade::with('product', 'buyer')
            ->where(function ($query) {
                $query->where('buyer_id', Auth::id())
                    ->orWhereHas('product', function ($q) {
                        $q->where('user_id', Auth::id());
                    });
            })
            ->where('id', '<>', $trade->id)
            ->where('status', '!=', 'completed')
            ->get();

        return view('trades.show', compact(
            'trade',
            'partner',
            'messages',
            'product',
            'alreadyRated',
            'sidebarTrades'
        ));
    }

    /**
     * メッセージ送信
     */
    public function storeMessage(Request $request, Trade $trade)
    {
        $request->validate([
            'body' => 'required|max:400',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $data = [
            'trade_id' => $trade->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('trade_images', 'public');
        }

        TradeMessage::create($data);

        return back()->withInput();
    }

    public function editMessage(TradeMessage $message)
    {
        if ($message->user_id !== Auth::id()) abort(403);
        return view('trades.edit_message', compact('message'));
    }

    public function updateMessage(Request $request, TradeMessage $message)
    {
        if ($message->user_id !== Auth::id()) abort(403);

        $request->validate([
            'body' => 'required|max:400',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $message->body = $request->body;

        if ($request->hasFile('image')) {
            if ($message->image) {
                \Storage::disk('public')->delete($message->image);
            }
            $message->image = $request->file('image')->store('trade_images', 'public');
        }

        $message->save();

        return redirect()->route('trades.show', $message->trade_id)
            ->with('success', 'メッセージを更新しました');
    }

    public function destroyMessage(TradeMessage $message)
    {
        if ($message->user_id !== Auth::id()) abort(403);

        if ($message->image) {
            \Storage::disk('public')->delete($message->image);
        }

        $message->delete();
        return back()->with('success', 'メッセージを削除しました');
    }

    /**
     * 取引完了・評価送信
     */
    public function complete(Request $request, Trade $trade)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $product = $trade->product;

        $ratedUserId = Auth::id() === $trade->buyer_id
            ? $product->user_id
            : $trade->buyer_id;

        $alreadyRated = TradeRating::where('product_id', $product->id)
            ->where('rater_user_id', Auth::id())
            ->exists();

        if ($alreadyRated) {
            return back()->with('info', 'すでに評価済みです');
        }

        TradeRating::create([
            'product_id' => $product->id,
            'rater_user_id' => Auth::id(),
            'rated_user_id' => $ratedUserId,
            'rating' => $request->rating,
        ]);

        // 購入者 or 出品者の完了状態を分ける
        if (Auth::id() === $trade->buyer_id) {
            $trade->buyer_completed = true;
        } else {
            $trade->seller_completed = true;
        }

        // 両者完了で status を completed にする
        if ($trade->buyer_completed && $trade->seller_completed) {
            $trade->status = 'completed';
        }

        $trade->save();

        // メール送信（購入者完了でも送信してOK）
        if ($product) {
            Mail::to($product->user->email)->send(new TradeCompletedMail($trade));
        }

        return redirect()->route('products.index')
            ->with('success', '取引完了＆評価を送信しました');
    }
}
