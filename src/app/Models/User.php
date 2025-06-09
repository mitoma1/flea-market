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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nickname',           // ← 追加
        'is_profile_setup',   // ← 追加
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorite_product_user')->withTimestamps();
    }
    public function purchasedProducts()
    {
        // 購入履歴用の中間テーブル名を 'product_user_purchases' として想定
        return $this->belongsToMany(Product::class, 'product_user_purchases')->withTimestamps();
    }
}
