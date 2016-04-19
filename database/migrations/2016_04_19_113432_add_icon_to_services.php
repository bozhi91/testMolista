<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIconToServices extends Migration
{
	public function up()
	{
		Schema::table('services', function(Blueprint $table)
		{
			$table->string('icon')->after('id');
		});
	}

	public function down()
	{
		Schema::table('services', function(Blueprint $table)
		{
			$table->dropColumn('icon');
		});
	}
}
