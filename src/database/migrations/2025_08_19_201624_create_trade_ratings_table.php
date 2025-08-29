<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeRatingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('trade_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_id')->constrained()->onDelete('cascade'); // 取引単位
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // 対象商品
            $table->foreignId('rater_user_id')->constrained('users')->onDelete('cascade'); // 評価したユーザー
            $table->foreignId('rated_user_id')->constrained('users')->onDelete('cascade'); // 評価されたユーザー
            $table->tinyInteger('rating'); // 1〜5
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_ratings');
    }
}
