<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToServices extends Migration
{
	public function up()
	{
		Schema::table('services', function(Blueprint $table)
		{
			$table->string('code')->after('id')->index();
		});

		\DB::update("UPDATE services SET `code` = CONCAT('service-',`id`)");

		Schema::table('services', function(Blueprint $table)
		{
			$table->unique('code');
		});
	}

	public function down()
	{
		Schema::table('services', function(Blueprint $table)
		{
			$table->dropColumn('code');
		});
	}
}
