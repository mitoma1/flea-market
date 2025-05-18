<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category; // Ensure the Category model exists in the specified namespace
use App\Models\Comment; // Ensure the Comment model exists in the specified namespace

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'brand',
        'condition',
        'description',
        'price',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
