<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            
            // column
            $table->integer('user_id')->comment('user_id');
            $table->increments('seq')->comment('seq');
            $table->date('start')->comment('開始日');
            $table->date('end')->nullable()->comment('終了日');
            $table->string('system')->nullable()->comment('システム名');
            $table->string('role')->nullable()->comment('役割');
            $table->string('phase')->nullable()->comment('工程');
            $table->string('lang')->nullable()->comment('言語');
            $table->string('db')->nullable()->comment('データベース');
            $table->string('env')->nullable()->comment('環境');
            $table->string('overview')->nullable()->comment('業務概要');

            // timestamps
            $table->timestamps();

            // PK
            $table->primary(['user_id', 'seq']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('details');
    }
}
