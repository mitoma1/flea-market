<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Message;

class TradeSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------
        // 既存ユーザーを取得
        // -------------------
        $sato = User::where('email', 'sato@example.com')->first();
        $suzuki = User::where('email', 'suzuki@example.com')->first();

        if (!$sato || !$suzuki) {
            $this->command->info('佐藤 または 鈴木 が存在しません。Seeder を先に実行してください。');
            return;
        }

        // -------------------
        // 佐藤が出品した商品（腕時計）を取引中に設定
        // -------------------
        $product = Product::where('name', '腕時計')->first();
        if ($product) {
            $product->buyer_id = $suzuki->id; // 購入者
            $product->status = 'trading';     // 取引中ステータス
            $product->save();

            // -------------------
            // チャットメッセージ作成
            // -------------------
            Message::create([
                'product_id' => $product->id,
                'user_id' => $suzuki->id,
                'message' => '購入希望です。よろしくお願いします。',
            ]);

            Message::create([
                'product_id' => $product->id,
                'user_id' => $sato->id,
                'message' => 'ありがとうございます。支払いの準備ができたら教えてください。',
            ]);

            Message::create([
                'product_id' => $product->id,
                'user_id' => $suzuki->id,
                'message' => '了解です。今夜中に支払いします。',
            ]);
        } else {
            $this->command->info('腕時計の商品が存在しません。先に ProductSeeder を実行してください。');
        }
    }
}
