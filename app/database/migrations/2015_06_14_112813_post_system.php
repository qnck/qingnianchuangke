<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PostSystem extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posts', function($table){
			$table->increments('p_id');	//帖子id
			$table->string('p_title', 140);	//描述（纯文字信息，或者图片/视频的描述）
			$table->string('p_content', 100)->nullable();	//图片/视频地址
			$table->integer('p_praise')->nullable();	//赞数
			$table->integer('p_reply_count')->nullable();	//评论数
			$table->string('p_longitude', 11)->nullable();	//发送时的纬度
			$table->string('p_latitude', 11)->nullable();	//发送时的经度
			$table->string('p_address', 100)->nullable();	//发送地址
			$table->integer('u_id');	//用户id
			$table->tinyInteger('t_status');	//状态 0-正常， 1－作废
			$table->smallInteger('s_id');	//站点id
			$table->timestamp('created_at');	//创建时间
		});

		Schema::create('replys', function($table){
			$table->increments('r_id');	//回复id
			$table->timestamp('reply_at');	//回复时间
			$table->string('content', 140);	//内容
			$table->integer('u_id');	//用户id
			$table->tinyInteger('r_status');	//状态 0-正常 1-作废
			$table->morphs('repliable');	//多态关系 其中包涵 repliable_id & repliable_type
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('posts');
		Schema::dropIfExists('replys');
	}

}
