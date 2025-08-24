<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // 商品に紐づく
            $table->foreignId('user_id')->constrained()->onDelete('cascade');    // 送信ユーザー
            $table->text('message');                                              // メッセージ本文
            $table->boolean('is_read')->default(false);                           // 未読/既読フラグ
            $table->timestamps();                                                 // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
