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
            ->withPivot('payment'); // payment 情報も取得可能
    }
}
