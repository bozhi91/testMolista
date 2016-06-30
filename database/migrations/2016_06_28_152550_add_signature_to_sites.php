<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSignatureToSites extends Migration
{
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->text('signature');
		});
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('signature');
		});
	}
}
