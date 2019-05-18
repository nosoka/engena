<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameUnusedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('pass_types', 'delete_pass_types');
        Schema::rename('reserve_passes', 'delete_reserve_passes');
        Schema::rename('tblaccess', 'delete_tblaccess');
        Schema::rename('tblhashresources', 'delete_tblhashresources');
        Schema::rename('tbluserdaypasses', 'delete_tbluserdaypasses');
        Schema::rename('tbluserlongtermpasses', 'delete_tbluserlongtermpasses');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('delete_pass_types', 'pass_types');
        Schema::rename('delete_reserve_passes', 'reserve_passes');
        Schema::rename('delete_tblaccess', 'tblaccess');
        Schema::rename('delete_tblhashresources', 'tblhashresources');
        Schema::rename('delete_tbluserdaypasses', 'tbluserdaypasses');
        Schema::rename('delete_tbluserlongtermpasses', 'tbluserlongtermpasses');
    }
}
