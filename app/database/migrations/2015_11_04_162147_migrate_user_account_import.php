<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateUserAccountImport extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_import_qqs', function ($table) {
            $table->integer('u_id');
            $table->string('u_ext_id');
            $table->string('u_ext_token', 511);
            $table->string('u_head_img', 511);
            $table->string('u_nick_name');
            $table->tinyInteger('u_gender');
            $table->primary('u_id');
        });

        Schema::create('user_import_wechats', function ($table) {
            $table->integer('u_id');
            $table->string('u_ext_id');
            $table->string('u_ext_token', 511);
            $table->string('u_head_img', 511);
            $table->string('u_nick_name');
            $table->tinyInteger('u_gender');
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
