<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'body',      // ← message → body に修正
        'image',     // ← 画像カラムを追加
        'is_read',   // 未読既読フラグ
    ];

    // どの商品に紐づくか
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // どのユーザーが送信したか
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
