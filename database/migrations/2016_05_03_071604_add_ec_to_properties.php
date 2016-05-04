<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEcToProperties extends Migration
{
	public function up()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->boolean('ec_pending')->default(1)->after('highlighted');
			$table->string('ec')->after('highlighted');
		});
	}

	public function down()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropColumn('ec');
			$table->dropColumn('ec_pending');
		});
	}
}
