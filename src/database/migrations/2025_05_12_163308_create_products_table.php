<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 出品者
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // カテゴリ
            $table->string('name');   // 商品名
            $table->string('brand')->nullable();  // ブランド（任意）
            $table->string('condition'); // 商品状態（新品、目立った傷なし、など）
            $table->text('description'); // 商品説明
            $table->integer('price');   // 価格
            $table->string('image');    // 商品画像のパス
            $table->timestamps();      // 作成日時、更新日時
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
