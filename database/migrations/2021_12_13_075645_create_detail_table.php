<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->integer('h_id')->comment('ヘッダid');
            $table->date('start')->comment('開始日');
            $table->date('end')->nullable()->comment('終了日');
            $table->string('industry')->nullable()->comment('業界');
            $table->string('system')->nullable()->comment('システム名');
            $table->string('role')->nullable()->comment('役割');
            $table->string('phase')->nullable()->comment('工程');
            $table->string('overview')->nullable()->comment('業務概要');
            $table->string('lang')->nullable()->comment('言語');
            $table->string('db')->nullable()->comment('データベース');
            $table->string('env')->nullable()->comment('環境');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail');
    }
}
