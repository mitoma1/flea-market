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

    // 出品商品
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // お気に入り商品
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorite_product_users')->withTimestamps();
    }

    // 購入商品
    public function purchasedProducts()
    {
        return $this->belongsToMany(Product::class, 'purchases', 'user_id', 'product_id')
            ->withTimestamps()
            ->withPivot('payment');
    }

    // プロフィール画像URL
    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : asset('images/default-profile.png');
    }

    // 取引
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    public function sellingTrades()
    {
        return $this->hasMany(Trade::class, 'seller_id');
    }

    public function buyingTrades()
    {
        return $this->hasMany(Trade::class, 'buyer_id');
    }

    // 受けた評価
    public function receivedRatings()
    {
        return $this->hasMany(TradeRating::class, 'rated_user_id');
    }

    // 平均評価
    public function averageRating()
    {
        return round($this->receivedRatings()->avg('rating') ?? 0);
    }
}
