<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $userIds = User::pluck('id')->toArray();

        if (count($userIds) < 1) {
            $this->command->info("ユーザーが存在しません。");
            return;
        }

        $products = [
            ['腕時計', 'watch.jpg', 15000, 'コンディション良好', 'スタイリッシュなデザインのメンズ腕時計'],
            ['HDD', 'hdd.jpg', 5000, '目立った傷や汚れなし', '高速で信頼性の高いハードディスク'],
            ['玉ねぎ3束', 'onion.jpg', 300, 'やや傷や汚れあり', '新鮮な玉ねぎ３玉のセット'],
            ['革靴', 'shoes.jpg', 4000, '状態が悪い', 'クラシックなデザインの革靴'],
            ['ノートPC', 'laptop.jpg', 45000, 'コンディション良好', '高性能なノートパソコン'],
            ['マイク', 'microphone.jpg', 8000, '目立った傷や汚れなし', '高音質のレコーディング用マイク'],
            ['ショルダーバッグ', 'shoulder_bag.jpg', 3500, 'やや傷や汚れあり', 'おしゃれなショルダーバッグ'],
            ['タンブラー', 'tumbler.jpg', 500, '状態が悪い', '使いやすいタンブラー'],
            ['コーヒーミル', 'coffee_mill.jpg', 4000, 'コンディション良好', '手動のコーヒーミル'],
            ['メイクセット', 'makeup_set.jpg', 2500, '目立った傷や汚れなし', '便利なメイクアップセット'],
        ];

        foreach ($products as $p) {
            DB::table('products')->insert([
                'user_id' => $faker->randomElement($userIds),
                'buyer_id' => null, // 空チャット状態
                'name' => $p[0],
                'image' => $p[1],
                'price' => $p[2],
                'condition' => $p[3],
                'description' => $p[4],
                'status' => 'selling', // 全て販売中
            ]);
        }
    }
}
