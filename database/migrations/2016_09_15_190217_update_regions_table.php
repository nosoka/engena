<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('tblregions', 'regions');
        Schema::table('regions', function ($table) {
            $table->renameColumn('ID', 'id');
            $table->renameColumn('Region', 'name');
            $table->renameColumn('Country', 'country');
            $table->renameColumn('StateProvince', 'state');
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });

        Schema::table('tblreserves', function ($table) {
            $table->renameColumn('RegionID', 'region_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('regions', 'tblregions');

        Schema::table('tblregions', function ($table) {
            $table->renameColumn('id', 'ID');
            $table->renameColumn('name', 'Region');
            $table->renameColumn('country', 'Country');
            $table->renameColumn('state', 'StateProvince');
        });

        Schema::table('tblregions', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('tblreserves', function ($table) {
            $table->renameColumn('region_id', 'RegionID');
        });
    }
}
