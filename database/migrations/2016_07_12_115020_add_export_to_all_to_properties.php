<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExportToAllToProperties extends Migration
{
	public function up()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->boolean('export_to_all')->after('construction_year');
		});
	}

	public function down()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropColumn('export_to_all');
		});
	}
}
