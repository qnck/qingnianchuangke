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
			$table->increments('u_id');
			$table->string('u_mobile', 11);
			$table->string('u_password', 128);
			$table->string('u_nickname', 32)->nullable();
			$table->smallInteger('u_age')->nullable();
			$table->string('u_name', 16)->nullable();
			$table->tinyInteger('u_sex')->nullable();
			$table->integer('u_head_img')->nullable();
			$table->string('u_identity_number', 18)->nullable();
			$table->integer('u_identity_img')->nullable();
			$table->string('u_school_name', 64)->nullable();
			$table->string('u_student_number', 32)->nullable();
			$table->integer('u_student_img')->nullable();
			$table->string('u_address', 128)->nullable();
			$table->tinyInteger('u_status')->nullable();
			$table->string('u_token', 128)->nullable();
			$table->timestamps();
		});

		Schema::create('txt_messages', function($table){
			$table->increments('t_id');
			$table->string('t_mobile', 11);
			$table->string('t_content', 256);
			$table->tinyInteger('t_send_level');
			$table->timestamp('send_at');
			$table->timestamps();
		});

		Schema::create('verification_codes', function($table){
			$table->increments('v_id');
			$table->string('v_code');
			$table->tinyInteger('v_reuse');
			$table->timestamp('verify_at')->nullable();
			$table->timestamp('expire_at');
			$table->morphs('verifiable');
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
