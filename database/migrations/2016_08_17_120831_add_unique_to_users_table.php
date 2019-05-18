<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('tblusers', function (Blueprint $table) {
            $table->unique('Email');
            $table->unique('Mobile');
            $table->unique('Username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblusers', function (Blueprint $table) {
            $table->dropUnique('tblusers_email_unique');
            $table->dropUnique('tblusers_mobile_unique');
            $table->dropUnique('tblusers_username_unique');
        });
    }
}
