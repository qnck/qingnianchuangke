<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigratePatch2015111301 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->tinyInteger('u_is_verified');        // 是否认校园证用户
        });

        Schema::create('organization', function ($table) {
            $table->interger('u_id');
            $table->string('o_titile', 63);
            $table->text('o_brief');
            $table->string('o_office_url');
            $table->tinyInteger('o_status');    // 组织状态 1-待审核, 2-审核通过, 3-审核不通过
            $table->string('o_imgs', 2047);
            $table->timestamp('created_at');
            $table->tinyInteger('o_type');      //组织类型, 1-青创官方 2-普通组织
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
