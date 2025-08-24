<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeRating extends Model
{
    use HasFactory;

    protected $fillable = ['trade_id', 'product_id', 'rater_user_id', 'rated_user_id', 'rating'];

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_user_id');
    }

    public function rated()
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }
}
