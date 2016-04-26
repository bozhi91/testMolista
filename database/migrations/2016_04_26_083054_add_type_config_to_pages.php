<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeConfigToPages extends Migration
{
	public function up()
	{
		Schema::table('pages', function(Blueprint $table)
		{
			$table->text('configuration')->after('site_id');
			$table->string('type')->default('default')->after('site_id');
		});
	}

	public function down()
	{
		Schema::table('pages', function(Blueprint $table)
		{
			$table->dropColumn('type');
			$table->dropColumn('configuration');
		});
	}
}
