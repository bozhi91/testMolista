<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanViewAllToSitesUsers extends Migration
{
	public function up()
	{
		Schema::table('sites_users', function(Blueprint $table)
		{
			$table->boolean('can_view_all')->default(1);
		});
	}

	public function down()
	{
		Schema::table('sites_users', function(Blueprint $table)
		{
			$table->dropColumn('can_view_all');
		});
	}
}
