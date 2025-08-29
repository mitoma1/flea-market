<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nickname',
        'is_profile_setup',
        'postcode',
        'address',
        'building',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // -------------------------------
    // 商品・取引関連
    // -------------------------------

    /**
     * 出品商品（1対多）
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * お気に入り商品（多対多）
     * 中間テーブル名は favorite_product_users
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorite_product_users')->withTimestamps();
    }

    /**
     * 購入商品（多対多: purchases 経由）
     */
    public function purchasedProducts()
    {
        return $this->belongsToMany(Product::class, 'purchases', 'user_id', 'product_id')
            ->withTimestamps()
            ->withPivot('payment');
    }

    /**
     * 取引全体
     */
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * 出品者としての取引
     */
    public function sellingTrades()
    {
        return $this->hasMany(Trade::class, 'seller_id');
    }

    /**
     * 購入者としての取引
     */
    public function buyingTrades()
    {
        return $this->hasMany(Trade::class, 'buyer_id');
    }

    // -------------------------------
    // プロフィール画像
    // -------------------------------

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : asset('images/default-profile.png');
    }

    // -------------------------------
    // 評価関連（統合）
    // -------------------------------

    /**
     * 受け取った評価
     */
    public function receivedRatings()
    {
        return $this->hasMany(TradeRating::class, 'rated_user_id');
    }

    /**
     * 平均評価（★の数）
     */
    public function averageRating()
    {
        $avg = $this->receivedRatings()->avg('rating');
        return $avg ? round($avg) : 0;
    }

    /**
     * 評価件数
     */
    public function ratingsCount()
    {
        return $this->receivedRatings()->count();
    }

    // -------------------------------
    // 取引中商品
    // -------------------------------

    public function tradingProducts()
    {
        return Product::whereHas('trade', function ($q) {
            $q->where('status', 'in_progress')
                ->where(function ($q2) {
                    $q2->where('buyer_id', $this->id)
                        ->orWhereHas('product', fn($q3) => $q3->where('user_id', $this->id));
                });
        })
            ->with(['trade.messages' => fn($q) => $q->orderBy('created_at', 'asc')])
            ->get()
            ->map(function ($product) {
                $product->unread_messages_count = optional($product->trade)
                    ->messages
                    ->where('user_id', '!=', $this->id)
                    ->where('is_read', false)
                    ->count();
                return $product;
            })
            ->sortByDesc(fn($product) => optional($product->trade)->messages->max('created_at') ?? optional($product->trade)->created_at)
            ->values();
    }
}
