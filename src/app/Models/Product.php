<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // 保存可能なカラム
    protected $fillable = [
        'user_id',
        'category_id',  // もし多対多なら別途 categories() リレーションで対応
        'name',
        'brand',
        'condition',
        'description',
        'price',
        'image',
    ];

    /**
     * 出品者（User）とのリレーション（多対1）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * コメントとのリレーション（1対多）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * お気に入りしたユーザーとのリレーション（多対多）
     * 中間テーブル名は favorite_product_users
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
