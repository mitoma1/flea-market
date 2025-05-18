<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BIGINT + auto_increment
            $table->string('name'); // VARCHAR(255)
            $table->string('email')->unique(); // VARCHAR(255) + UNIQUE
            $table->string('password'); // VARCHAR(255)
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


        Schema::dropIfExists('users'); // テーブルを削除


    }
}
