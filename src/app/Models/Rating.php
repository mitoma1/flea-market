<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trade_id',
        'rating',
        'comment',
    ];

    // 評価されたユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 評価元の取引
    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }
}
