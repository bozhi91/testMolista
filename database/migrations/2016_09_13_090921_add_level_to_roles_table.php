<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLevelToRolesTable extends Migration
{
	public function up()
	{
		Schema::table('roles', function(Blueprint $table) {
			$table->integer('level')->after('description');
		});
	}

	public function down()
	{
		Schema::table('roles', function(Blueprint $table)
		{
			$table->dropColumn('level');
		});
	}
}
