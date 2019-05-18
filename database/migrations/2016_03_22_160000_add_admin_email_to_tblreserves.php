<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdminEmailToTblReserves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblreserves', function (Blueprint $table) {
            $table->string('Admin_Email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblreserves', function (Blueprint $table) {
            $table->dropColumn('Admin_Email');
        });
    }
}
