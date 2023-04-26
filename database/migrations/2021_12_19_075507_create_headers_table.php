<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('headers', function (Blueprint $table) {

            // column
            $table->integer('user_id')->comment('user_id');
            $table->string('name')->comment('氏名');
            $table->string('initial')->comment('イニシャル');
            $table->boolean('disp_name')->nullable()->comment('氏名表示');
            $table->string('sex')->comment('性別');
            $table->date('birthday')->nullable()->comment('生年月日');
            $table->string('station')->nullable()->comment('最寄り駅');
            $table->string('cert')->nullable()->comment('資格');

            // timestamps
            $table->timestamps();

            // PK
            $table->primary(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('headers');
    }
}
