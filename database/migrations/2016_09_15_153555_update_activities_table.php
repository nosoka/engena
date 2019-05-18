<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('tblactivities', 'activities');
        Schema::table('activities', function ($table) {
            $table->renameColumn('ID', 'id');
            $table->renameColumn('Activity', 'name');
            $table->string('description', 500);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });

        Schema::table('tblreserveactivities', function ($table) {
            // $table->dropIndex('ActivityIDactivities_idx');
            $table->renameColumn('ActivityID', 'activity_id');
        });

        Schema::table('tbltrails', function ($table) {
            // $table->dropIndex('ActivityIDtrail_idx');
            $table->renameColumn('ActivityID', 'activity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('activities', 'tblactivities');

        Schema::table('tblactivities', function ($table) {
            $table->renameColumn('id', 'ID');
            $table->renameColumn('name', 'Activity');
        });

        Schema::table('tblactivities', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('tblreserveactivities', function ($table) {
            $table->renameColumn('activity_id', 'ActivityID');
        });
        Schema::table('tbltrails', function ($table) {
            $table->renameColumn('activity_id', 'ActivityID');
        });
    }
}
