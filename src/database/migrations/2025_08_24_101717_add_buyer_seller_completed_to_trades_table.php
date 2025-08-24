<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->boolean('buyer_completed')->default(false)->after('status');
            $table->boolean('seller_completed')->default(false)->after('buyer_completed');
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn(['buyer_completed', 'seller_completed']);
        });
    }
};
