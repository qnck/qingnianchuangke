<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DataBaseInit extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table){
			$table->increments('u_id');	//用户id
			$table->string('u_mobile', 11);	//手机号码
			$table->string('u_password', 128);	//密码
			$table->string('u_nickname', 32)->nullable();	//昵称
			$table->smallInteger('u_age')->nullable();	//年龄
			$table->string('u_name', 16)->nullable();	//姓名
			$table->tinyInteger('u_sex')->nullable();	//性别
			$table->integer('u_head_img')->nullable();	//头像图片
			$table->string('u_identity_number', 18)->nullable();	//身份证号码
			$table->integer('u_identity_img')->nullable();	//身份证图片
			$table->string('u_school_id', 64)->nullable();	//学校名称
			$table->string('u_student_number', 32)->nullable();	//学生证号码
			$table->integer('u_student_img')->nullable();	//学生证图片
			$table->string('u_address', 128)->nullable();	//地址
			$table->tinyInteger('u_status')->nullable();	//状态
			$table->string('u_token', 128)->nullable();	//token, 随机字符串， 每次登陆不一样
			$table->timestamps();
		});

		Schema::create('txt_messages', function($table){
			$table->increments('t_id');	//短信id
			$table->string('t_mobile', 11);	//手机号码
			$table->string('t_content', 256);	//发送内容
			$table->tinyInteger('t_send_level');	//发送级别 级别对应放在TxtMessage.php里面以类常量形式存在
			$table->timestamp('send_at');	//发送时间
			$table->timestamps();
		});

		Schema::create('verification_codes', function($table){
			$table->increments('v_id');	//验证码id
			$table->string('v_code');	//验证码
			$table->tinyInteger('v_reuse');	//是否可重复使用
			$table->timestamp('verify_at')->nullable();	//验证时间 默认为null, 若该域有值 则说明该码已经使用过
			$table->timestamp('expire_at');	//过期时间
			$table->morphs('verifiable');	//多太关系字段
			$table->timestamps();
		});

		DB::statement('ALTER TABLE `qnckdb`.`t_verification_codes` CHANGE COLUMN `verifiable_id` `verifiable_id` varchar(11) NOT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('t_users');
		Schema::dropIfExists('txt_messages');
		Schema::dropIfExists('verification_codes');
	}

}
