<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => '腕時計',
                'image' => 'watch.jpg',
                'price' => 15000,
                'condition' => 'コンディション良好',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
            ],
            [
                'name' => 'HDD',
                'image' => 'hdd.jpg',
                'price' => 5000,
                'condition' => '目立った傷や汚れなし',
                'description' => '高速で信頼性の高いハードディスク',
            ],
            [
                'name' => '玉ねぎ3束',
                'image' => 'onion.jpg',
                'price' => 300,
                'condition' => 'やや傷や汚れあり',
                'description' => '新鮮な玉ねぎ３玉のセット',
            ],
            [
                'name' => '革靴',
                'image' => 'shoes.jpg',
                'price' => 4000,
                'condition' => '状態が悪い',
                'description' => 'クラシックなデザインの革靴',
            ],
            [
                'name' => 'ノートPC',
                'image' => 'laptop.jpg',
                'price' => 45000,
                'condition' => 'コンディション良好',
                'description' => '高性能なノートパソコン',
            ],
            [
                'name' => 'マイク',
                'image' => 'microphone.jpg',
                'price' => 8000,
                'condition' => '目立った傷や汚れなし',
                'description' => '高音質のレコーディング用マイク',
            ],
            [
                'name' => 'ショルダーバッグ',
                'image' => 'shoulder_bag.jpg',
                'price' => 3500,
                'condition' => 'やや傷や汚れあり',
                'description' => 'おしゃれなショルダーバッグ',
            ],
            [
                'name' => 'タンブラー',
                'image' => 'tumbler.jpg',
                'price' => 500,
                'condition' => '状態が悪い',
                'description' => '使いやすいタンブラー',
            ],
            [
                'name' => 'コーヒーミル',
                'image' => 'coffee_mill.jpg',
                'price' => 4000,
                'condition' => 'コンディション良好',
                'description' => '手動のコーヒーミル',
            ],
            [
                'name' => 'メイクセット',
                'image' => 'makeup_set.jpg',
                'price' => 2500,
                'condition' => '目立った傷や汚れなし',
                'description' => '便利なメイクアップセット',
            ],
        ]);
    }
}
