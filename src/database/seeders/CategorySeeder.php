<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => '家電'],
            ['name' => 'ファッション'],
            ['name' => '食品'],
            ['name' => '本・雑誌'],
            ['name' => 'その他'],
        ]);
    }
}
