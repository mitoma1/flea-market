<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;

class TradeChatController extends Controller
{
    public function showSellerChat(Trade $trade)
    {
        if ($trade->product->user_id !== auth()->id()) {
            abort(403, 'アクセス権限がありません');
        }

        $trade->load('buyer', 'messages.user', 'product');

        return view('trades.seller_chat', [
            'trade' => $trade,
            'messages' => $trade->messages()->orderBy('created_at')->get(),
        ]);
    }
}
