<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // 外部キー制約を一時的にオフにする（MySQLの場合）
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 既存データを削除
        Category::truncate();

        // 外部キー制約をオンに戻す
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 登録したいカテゴリーの配列
        $categories = [
            ['name' => 'ファッション'],
            ['name' => '家電'],
            ['name' => 'インテリア'],
            ['name' => 'レディース'],
            ['name' => 'メンズ'],
            ['name' => '本'],
            ['name' => 'ゲーム'],
            ['name' => 'スポーツ'],
            ['name' => 'キッチン'],
            ['name' => 'ハンドメイド'],
            ['name' => 'アクセサリー'],
            ['name' => 'おもちゃ'],
            ['name' => 'ベビー・キッズ'],
            ['name' => 'その他'],
        ];

        // 1件ずつ作成
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
