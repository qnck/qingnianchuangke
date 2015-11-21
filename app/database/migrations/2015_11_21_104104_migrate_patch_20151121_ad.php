<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigratePatch20151121Ad extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function ($table) {
            $table->increments('e_id');
            $table->integer('o_id');        // 企业id
            $table->string('e_title', 63);      // 标题
            $table->string('cover_img', 1023);  // 封面图
            $table->string('url', 511);         //url
            $table->tinyInteger('e_range');     // 1-全国, 2-城市, 3-学校
            $table->tinyInteger('e_position');  // 1-众筹, 2-promo, 3-flea
            $table->timestamp('e_start_date');  // 开始时间
            $table->timestamp('e_end_date');    // 结束时间
            $table->timestamp('created_at');
            $table->tinyInteger('e_status');    // 状态 1-可用 2-不可用
        });

        Schema::create('event_ranges', function ($table) {
            $table->increments('t_id');
            $table->integer('e_id');    // event id
            $table->integer('s_id');    // 学校 id
            $table->integer('c_id');    // 城市 id
            $table->integer('p_id');    // 省份 id
        });

        Schema::create('advertisements', function ($table) {
            $table->increments('ad_id');    // 广告id
            $table->integer('o_id');        // 企业id
            $table->tinyInteger('ad_status');   // 状态 1-可用 2-不可用
            $table->timestamp('created_at');
        });

        Schema::table('users', function ($table) {
            $table->string('u_invite_code', 31);    // 邀请码
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
