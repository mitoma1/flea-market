<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->tinyInteger('buyer_rating')->nullable()->after('seller_completed');  // 購入者が出品者に付けた評価
            $table->tinyInteger('seller_rating')->nullable()->after('buyer_rating');   // 出品者が購入者に付けた評価
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn(['buyer_rating', 'seller_rating']);
        });
    }
};
