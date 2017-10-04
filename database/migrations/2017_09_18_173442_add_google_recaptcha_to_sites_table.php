<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoogleRecaptchaToSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->boolean('recaptcha_enabled')->nullable()->after('ga_account');
            $table->string('recaptcha_sitekey', 40)->nullable()->after('recaptcha_enabled');
            $table->string('recaptcha_secretkey', 40)->nullable()->after('recaptcha_sitekey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('recaptcha_enabled');
            $table->dropColumn('recaptcha_sitekey');
            $table->dropColumn('recaptcha_secretkey');
        });
    }
}
