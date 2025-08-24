<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('trade_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_id')->constrained()->onDelete('cascade'); // 取引単位に変更
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // 投稿ユーザー
            $table->text('body');                                               // メッセージ本文
            $table->string('image')->nullable();                                 // 画像
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_messages');
    }
}
