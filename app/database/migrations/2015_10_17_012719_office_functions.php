<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfficeFunctions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('u_remark', 511)->nullable();
        });

        Schema::table('products', function ($table) {
            $table->string('p_remark', 511)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('u_remark');
        });

        Schema::table('products', function ($table) {
            $table->dropColumn('p_remark');
        });
    }
}
