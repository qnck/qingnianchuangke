<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Migrate2015112502Notifications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function ($table) {
            $table->increments('n_id');
            $table->string('n_icon', 255)->nullable();
            $table->string('n_title', 63);
            $table->string('n_brief', 511)->nullable();
            $table->text('n_content')->nullable();
            $table->string('n_url', 511)->nullable();
            $table->tinyInteger('n_type');      //1-带跳转连接的 2-纯消息
            $table->tinyInteger('n_cate')->nullable();
            $table->tinyInteger('n_status');    // 1-可用 2-不可用
            $table->timestamp('created_at');
        });

        Schema::create('notification_receivers', function ($table) {
            $table->increments('id');
            $table->integer('n_id');
            $table->integer('to_id');       // 用 0 表示广播
            $table->tinyInteger('to_type');     // 1-用户 2-频道
        });

        Schema::create('notification_reads', function ($table) {
            $table->increments('id');       // 这张表只存已读信息
            $table->integer('n_id');
            $table->integer('u_id');
            $table->tinyInteger('is_read')->nullable();
            $table->tinyInteger('is_del')->nullable();
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
