<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PostUserFriendInviteLog extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_friend_invite_logs', function ($table) {
            $table->increments('id'); //log id
            $table->string('u_id', 11); //user id
            $table->string('friend_id', 11);  // friend id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_friend_invite_logs');
    }
}
