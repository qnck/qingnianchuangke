<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Migarate2015121101 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('features', function ($table) {
            $table->increments('id');
            $table->tinyInteger('sort')->default(0);
            $table->integer('featurable_id');
            $table->tinyInteger('featurable_cate');    // 推荐类型 1-众筹 2-商品 3-flea精品
            $table->timestamp('created_at');
            $table->tinyInteger('status');  //状态 1-有效 0-无效
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
