<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('header', function (Blueprint $table) {
            $table->increments('id')->comment('id');
            $table->string('name')->comment('氏名');
            $table->string('initial')->nullable()->comment('イニシャル');
            $table->string('sex')->nullable()->comment('性別');
            $table->date('birthday')->nullable()->comment('生年月日');
            $table->string('station')->nullable()->comment('最寄り駅');
            $table->string('cert')->nullable()->comment('資格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('header');
    }
}
