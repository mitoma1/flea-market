<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? 1,  // 既存ユーザーからランダム取得
            'name' => $this->faker->word() . ' 商品',
            'image' => 'default.jpg',
            'price' => $this->faker->numberBetween(500, 20000),
            'condition' => $this->faker->randomElement(['新品', '良好', '中古']),
            'description' => $this->faker->sentence(10),
            'buyer_id' => null,
        ];
    }
}
