<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlagsToProperties extends Migration
{
	public function up()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->boolean('highlighted')->after('enabled')->index();
			$table->boolean('second_hand')->after('enabled')->index();
			$table->boolean('newly_build')->after('enabled')->index();
		});
	}

	public function down()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropColumn('highlighted');
			$table->dropColumn('second_hand');
			$table->dropColumn('newly_build');
		});
	}
}
