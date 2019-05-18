<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrailFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->string('title',100)->nullable();
            $table->string('description',500)->nullable();
            $table->string('mime_type',50);
            $table->string('real_path',255);
            $table->string('url',255);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });

        Schema::create('trail_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trail_id')->unsigned();
            $table->integer('file_id')->unsigned();
            $table->boolean('is_primary');
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
        Schema::drop('files');
        Schema::drop('trail_files');
    }
}
