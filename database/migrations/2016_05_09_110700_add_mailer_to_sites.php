<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMailerToSites extends Migration
{
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->text('mailer')->after('favicon');
		});
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('mailer');
		});
	}
}
