<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSocialAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('user_social_accounts', function (Blueprint $table) {
            $table->renameColumn('provider_user_id', 'provider_id');
            $table->renameColumn('provider_access_token', 'token');
        });

        Schema::table('user_social_accounts', function ($table) {
            $table->string('token', 500)->change();
            $table->string('full_name')->after('token');
            $table->string('avatar')->after('full_name');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_social_accounts', function (Blueprint $table) {
            $table->renameColumn('provider_id', 'provider_user_id');
            $table->renameColumn('token', 'provider_access_token');
            $table->dropColumn('full_name');
            $table->dropColumn('avatar');
        });

    }
}
