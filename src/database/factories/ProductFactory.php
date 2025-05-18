<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'テスト商品',
            'image' => 'default.jpg',
            'price' => 1000,
            'condition' => '良好',
            'description' => 'テスト商品説明',
        ];
    }
}
