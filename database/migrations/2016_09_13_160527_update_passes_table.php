<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passes', function ($table) {
            $table->decimal('price', 5, 2)->after('description');
            $table->renameColumn('pass_type_id', 'reserve_id');
            $table->renameColumn('pass_duration_id', 'duration_id');
        });

        Schema::table('user_passes', function ($table) {
            $table->renameColumn('reserve_pass_id', 'pass_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('passes', function ($table) {
            $table->dropColumn('price');
            $table->renameColumn('reserve_id', 'pass_type_id');
            $table->renameColumn('duration_id', 'pass_duration_id');
        });
        Schema::table('user_passes', function ($table) {
            $table->renameColumn('pass_id', 'reserve_pass_id');
        });
    }
}
