<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanupReserves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblreserves', function (Blueprint $table) {
            $table->dropColumn('DayPassCost');
            $table->dropColumn('YearPassCost');
            $table->string('description', 500)->nullable()->change();
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
            $table->integer('DayPassCost')->after('description');
            $table->integer('YearPassCost')->after('DayPassCost');
        });
    }
}
