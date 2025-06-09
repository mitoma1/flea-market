<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB; // DBファサードを使用する場合は必要

class CategorySeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');  // 外部キー制約オフ
        Category::truncate(); // 全てのカテゴリーデータを削除
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');  // 外部キー制約オン

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

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
