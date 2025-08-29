<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'buyer_id', 'status', 'buyer_completed', 'seller_completed'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function messages()
    {
        return $this->hasMany(TradeMessage::class);
    }

    public function ratings()
    {
        return $this->hasMany(TradeRating::class);
    }

    /**
     * 指定ユーザーがこの取引を完了できるか判定
     */
    public function canBeCompletedBy($user)
    {
        // 購入者がまだ完了していない場合
        if ($user->id === $this->buyer_id && !$this->buyer_completed) {
            return true;
        }

        // 出品者がまだ完了していない場合（購入者が完了済み）
        if ($user->id === $this->product->user_id && $this->buyer_completed && !$this->seller_completed) {
            return true;
        }

        return false;
    }
}
