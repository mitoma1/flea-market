<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    // Category には複数の Product が属する（1対多）
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
