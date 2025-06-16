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
            $table->bigIncrements('id');
            $table->foreignId('user_id')->after('id');
            $table->string('name');            // 商品名
            $table->string('brand')->nullable(); // ブランド名（任意）
            $table->string('condition');       // 状態（例: 新品・中古）
            $table->text('description');       // 商品説明
            $table->integer('price');          // 価格
            $table->string('image');           // 商品画像のパス
            $table->timestamps();              // created_at / updated_at
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
