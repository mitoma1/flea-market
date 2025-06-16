<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Purchase;

class Product extends Model
{
    use HasFactory;

    // 保存可能なカラム
    protected $fillable = [
        'user_id',

        'category_id',  // ※多対多であれば不要な場合あり。使ってるなら残す
        'name',
        'brand',
        'condition',
        'description',
        'price',
        'image',

    ];

    /**
     * 出品者（ユーザー）とのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * コメントとのリレーション
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * お気に入り（いいね）したユーザーとのリレーション（多対多）
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_product_users')->withTimestamps();
    }

    /**
     * 購入情報とのリレーション（1対1）
     */
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    /**
     * 商品が購入済みかどうかを判定
     */
    public function isSold(): bool
    {
        return $this->purchase()->exists();
    }

    /**
     * カテゴリーとのリレーション（多対多）
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products')->withTimestamps();
    }
}
