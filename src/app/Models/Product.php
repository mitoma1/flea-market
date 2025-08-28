<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'buyer_id',
        'category_id',
        'name',
        'brand',
        'condition',
        'description',
        'price',
        'image', // public/images/配下
        'status',
    ];

    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 購入者
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // お気に入り
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_product_users')->withTimestamps();
    }

    // 購入情報
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    // 商品が購入済みかどうか
    public function isSold(): bool
    {
        return $this->purchase()->exists();
    }

    // カテゴリー
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products')->withTimestamps();
    }

    // 取引メッセージ
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // 商品画像URL（アクセサ）修正版
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/default-product.png');
        }

        $filename = basename($this->image); // フルパスからファイル名を取り出す
        return asset('images/' . $filename);
    }

    // 商品に紐づく取引
    public function trade()
    {
        return $this->hasOne(Trade::class);
    }
}
