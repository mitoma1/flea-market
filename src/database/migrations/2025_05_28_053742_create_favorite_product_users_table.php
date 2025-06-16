<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteProductUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorite_product_users', function (Blueprint $table) {
            $table->bigIncrements('id');

            // 外部キーを設定する前に、カラムを定義
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->timestamps();

            // 必要ならユニーク制約を追加（同じお気に入り重複防止）
            // $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorite_product_users');
    }
}
