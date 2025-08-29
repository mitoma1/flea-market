<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TradeMessageRequest;
use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\TradeRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeCompletedMail;
use App\Models\User;

class TradeController extends Controller
{
    // 取引チャット画面
    public function show(Trade $trade)
    {
        $trade->load('product.user', 'buyer', 'messages.user');

        // 取引相手を判定
        $partner = Auth::id() === $trade->product->user_id
            ? $trade->buyer
            : $trade->product->user;

        $messages = $trade->messages()->orderBy('created_at')->get();
        $product = $trade->product;

        // 自分が評価済みかどうか判定
        $alreadyRatedByMe = TradeRating::where('trade_id', $trade->id)
            ->where('rater_user_id', Auth::id())
            ->exists();

        // 相手ユーザーの平均評価（プロフィール用）
        $partnerRating = TradeRating::where('rated_user_id', $partner->id)->avg('rating');
        $partnerRatingRounded = $partnerRating !== null ? round($partnerRating) : null;

        // サイドバー用の進行中取引
        $sidebarTrades = Trade::with('product', 'buyer')
            ->where(function ($query) {
                $query->where('buyer_id', Auth::id())
                    ->orWhereHas('product', fn($q) => $q->where('user_id', Auth::id()));
            })
            ->where('id', '<>', $trade->id)
            ->where('status', '!=', 'completed')
            ->get();

        return view('trades.show', compact(
            'trade',
            'partner',
            'messages',
            'product',
            'alreadyRatedByMe',
            'sidebarTrades',
            'partnerRatingRounded'
        ));
    }

    // メッセージ送信
    public function storeMessage(TradeMessageRequest $request, Trade $trade)
    {
        $data = [
            'trade_id' => $trade->id,
            'user_id'  => Auth::id(),
            'body'     => $request->body,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('trade_images', 'public');
        }

        TradeMessage::create($data);

        return back()->withInput();
    }

    // メッセージ更新
    public function updateMessage(TradeMessageRequest $request, TradeMessage $message)
    {
        if ($message->user_id !== Auth::id()) abort(403);

        $message->body = $request->body;

        if ($request->hasFile('image')) {
            if ($message->image) \Storage::disk('public')->delete($message->image);
            $message->image = $request->file('image')->store('trade_images', 'public');
        }

        $message->save();

        return redirect()->route('trades.show', $message->trade_id)
            ->with('success', 'メッセージを更新しました');
    }

    // メッセージ削除
    public function destroyMessage(TradeMessage $message)
    {
        if ($message->user_id !== Auth::id()) abort(403);

        if ($message->image) \Storage::disk('public')->delete($message->image);
        $message->delete();

        return back()->with('success', 'メッセージを削除しました');
    }

    // 取引完了・評価送信
    public function complete(Request $request, Trade $trade)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $product = $trade->product;

        // 評価されるユーザーIDを判定
        $ratedUserId = Auth::id() === $trade->buyer_id
            ? $product->user_id
            : $trade->buyer_id;

        // すでに評価済みかチェック
        $alreadyRated = TradeRating::where('trade_id', $trade->id)
            ->where('rater_user_id', Auth::id())
            ->exists();

        if ($alreadyRated) {
            return back()->with('info', 'すでに評価済みです');
        }

        // 評価を作成
        TradeRating::create([
            'trade_id'       => $trade->id,
            'product_id'     => $product->id,
            'rater_user_id'  => Auth::id(),
            'rated_user_id'  => $ratedUserId,
            'rating'         => $request->rating,
        ]);

        // 取引完了フラグの更新
        if (Auth::id() === $trade->buyer_id) {
            $trade->buyer_completed = true;
            Mail::to($product->user->email)->send(new TradeCompletedMail($trade));
        } else {
            $trade->seller_completed = true;
        }

        // 両者完了ならステータスを completed に
        if ($trade->buyer_completed && $trade->seller_completed) {
            $trade->status = 'completed';
        }

        $trade->save();

        // 評価を受けたユーザーの平均評価を再計算して保存
        $ratedUser = User::find($ratedUserId);
        if ($ratedUser) {
            $averageRating = TradeRating::where('rated_user_id', $ratedUserId)->avg('rating');
            $ratedUser->average_rating = $averageRating !== null ? round($averageRating) : null;
            $ratedUser->save();
        }

        return redirect()->route('products.index')->with('success', '取引完了＆評価を送信しました');
    }
}
