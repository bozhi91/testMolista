<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewOpportunityToProperties extends Migration
{
	public function up()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->boolean('opportunity')->index()->after('highlighted');
			$table->boolean('new_item')->index()->after('highlighted');
		});
	}

	public function down()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropColumn('new_item');
			$table->dropColumn('opportunity');
		});
	}
}
