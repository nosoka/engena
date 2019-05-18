<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanupUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblusers', function (Blueprint $table) {
            $table->dropColumn('Units');
            $table->dropColumn('Distance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblusers', function ($table) {
            $table->integer('Units')->unsigned()->nullable()->after('status');
            $table->integer('Distance')->unsigned()->nullable()->after('Units');
        });
    }
}
