<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomThemeToSites extends Migration
{
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->string('custom_theme')->after('theme')->nullable();
		});
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('custom_theme');
		});
	}
}
