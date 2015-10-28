<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserProfile extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profile_bases', function ($table) {
            $table->integer('u_id');
            $table->tinyInteger('u_status');    //状态 0：未审核 1：已审核(已审核通过不允许再编辑)
            $table->string('u_id_number', 18);
            $table->string('u_id_imgs', 700);
            $table->tinyInteger('u_is_id_verified');
            $table->integer('s_id');
            $table->string('u_entry_year', 16);
            $table->string('u_major', 128);
            $table->string('u_student_number', 32);
            $table->string('u_student_imgs', 700);
            $table->tinyInteger('u_is_student_verified');
            $table->string('em_contact_phone', 16);
            $table->string('em_contact_name', 16);
            $table->string('u_father_name', 16);
            $table->string('u_father_phone', 16);
            $table->string('u_mother_name', 16);
            $table->string('u_mother_phone', 16);
            $table->string('u_home_address', 16);
            $table->primary('u_id');
        });

        Schema::create('tmp_user_profile_bases', function ($table) {
            $table->integer('u_id');
            $table->tinyInteger('u_status');    //状态 0：未审核 1：已审核(已审核通过不允许再编辑)
            $table->string('u_id_number', 18);
            $table->string('u_id_imgs', 700);
            $table->tinyInteger('u_is_id_verified');
            $table->integer('s_id');
            $table->string('u_entry_year', 16);
            $table->string('u_major', 128);
            $table->string('u_student_number', 32);
            $table->string('u_student_imgs', 700);
            $table->tinyInteger('u_is_student_verified');
            $table->string('em_contact_phone', 16);
            $table->string('em_contact_name', 16);
            $table->string('u_father_name', 16);
            $table->string('u_father_phone', 16);
            $table->string('u_mother_name', 16);
            $table->string('u_mother_phone', 16);
            $table->string('u_home_address', 16);
            $table->string('remark', 512);
            $table->primary('u_id');
        });

        Schema::create('user_profile_bankcards', function ($table) {
            $table->integer('u_id');
            $table->integer('b_id');
            $table->string('b_card_number', 20);
            $table->string('b_holder_name', 20);
            $table->string('b_holder_phone', 20);
            $table->string('b_holder_id_number', 18);
            $table->tinyInteger('b_status');    //状态: 0 待审核, 1 审核通过, 2 审核不通过
            $table->primary('u_id');
        });

        Schema::create('tmp_user_profile_bankcards', function ($table) {
            $table->integer('u_id');
            $table->integer('b_id');
            $table->string('b_card_number', 20);
            $table->string('b_holder_name', 20);
            $table->string('b_holder_phone', 20);
            $table->string('b_holder_id_number', 18);
            $table->tinyInteger('b_status');    //状态: 0 待审核, 1 审核通过, 2 审核不通过
            $table->string('remark', 512);
            $table->primary('u_id');
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
