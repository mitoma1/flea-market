<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Trade;
use Illuminate\Support\Facades\Hash;

class UserProductTradeSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------
        // ユーザー作成
        // -------------------
        $sato = User::firstOrCreate(
            ['email' => 'sato@example.com'],
            ['name' => '佐藤', 'password' => Hash::make('password123')]
        );

        $suzuki = User::firstOrCreate(
            ['email' => 'suzuki@example.com'],
            ['name' => '鈴木', 'password' => Hash::make('password123')]
        );

        $moriyama = User::firstOrCreate(
            ['email' => 'moriyama@example.com'],
            ['name' => '森山', 'password' => Hash::make('password123')]
        );

        // -------------------
        // 佐藤さんの商品
        // -------------------
        $satoProductsData = [
            ['name' => '腕時計', 'price' => 15000, 'description' => 'スタイリッシュなデザインのメンズ腕時計', 'image' => 'watch.jpg', 'condition' => '良好'],
            ['name' => 'HDD', 'price' => 5000, 'description' => '高速で信頼性の高いハードディスク', 'image' => 'hdd.jpg', 'condition' => '良好'],
            ['name' => '玉ねぎ3束', 'price' => 300, 'description' => '新鮮な玉ねぎ3束のセット', 'image' => 'onion.jpg', 'condition' => '良好'],
            ['name' => '革靴', 'price' => 4000, 'description' => 'クラシックなデザインの革靴', 'image' => 'shoes.jpg', 'condition' => '良好'],
            ['name' => 'ノートPC', 'price' => 45000, 'description' => '高性能なノートパソコン', 'image' => 'laptop.jpg', 'condition' => '良好'],
        ];

        $satoProducts = [];
        foreach ($satoProductsData as $data) {
            $satoProducts[] = Product::firstOrCreate(
                ['name' => $data['name'], 'user_id' => $sato->id],
                $data
            );
        }

        // -------------------
        // 鈴木さんの商品
        // -------------------
        $suzukiProductsData = [
            ['name' => 'マイク', 'price' => 8000, 'description' => '高音質のレコーディング用マイク', 'image' => 'microphone.jpg', 'condition' => '良好'],
            ['name' => 'ショルダーバッグ', 'price' => 3500, 'description' => 'おしゃれなショルダーバッグ', 'image' => 'shoulder_bag.jpg', 'condition' => '良好'],
            ['name' => 'タンブラー', 'price' => 500, 'description' => '使いやすいタンブラー', 'image' => 'tumbler.jpg', 'condition' => '良好'],
            ['name' => '手動コーヒーミル', 'price' => 4000, 'description' => '手動のコーヒーミル', 'image' => 'coffee_mill.jpg', 'condition' => '良好'],
            // ★ 修正：ここだけ "makeup.jpg" に変更
            ['name' => 'メイクアップセット', 'price' => 2500, 'description' => '便利なメイクアップセット', 'image' => 'makeup.jpg', 'condition' => '良好'],
        ];

        $suzukiProducts = [];
        foreach ($suzukiProductsData as $data) {
            $suzukiProducts[] = Product::firstOrCreate(
                ['name' => $data['name'], 'user_id' => $suzuki->id],
                $data
            );
        }

        // -------------------
        // 取引作成（空チャット状態）
        // -------------------
        $tradesData = [
            // 佐藤 → 鈴木
            ['product' => $satoProducts[0], 'buyer' => $suzuki],
            ['product' => $satoProducts[1], 'buyer' => $suzuki],
            ['product' => $satoProducts[2], 'buyer' => $suzuki],
            ['product' => $satoProducts[3], 'buyer' => $suzuki],
            ['product' => $satoProducts[4], 'buyer' => $suzuki],
            // 鈴木 → 佐藤
            ['product' => $suzukiProducts[0], 'buyer' => $sato],
            ['product' => $suzukiProducts[1], 'buyer' => $sato],
            ['product' => $suzukiProducts[2], 'buyer' => $sato],
            ['product' => $suzukiProducts[3], 'buyer' => $sato],
            ['product' => $suzukiProducts[4], 'buyer' => $sato],
        ];

        foreach ($tradesData as $t) {
            if ($t['product'] && $t['buyer']) {
                Trade::firstOrCreate(
                    [
                        'product_id' => $t['product']->id,
                        'buyer_id' => $t['buyer']->id
                    ],
                    ['status' => 'in_progress']
                );
            }
        }
    }
}
