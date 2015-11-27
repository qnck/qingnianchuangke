<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Migrate2015112701 extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = User::whereNull('u_invite_code')->orWhere('u_invite_code', '=', '')->get();
        foreach ($list as $key => $user) {
            $user->u_invite_code = $user->getInviteCode();
            $user->save();
        }

        Schema::create('log_user_invite_code', function ($table) {
            $table->increments('id');
            $table->
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
