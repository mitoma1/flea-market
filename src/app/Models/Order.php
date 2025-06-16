<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'product_id', 'purchase_id'];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 商品とのリレーション
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 購入情報とのリレーション
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
