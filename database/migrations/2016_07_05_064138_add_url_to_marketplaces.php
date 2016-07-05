<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlToMarketplaces extends Migration
{
	public function up()
	{
		Schema::table('marketplaces', function(Blueprint $table)
		{
			$table->text('url')->after('logo');
		});
	}

	public function down()
	{
		Schema::table('marketplaces', function(Blueprint $table)
		{
			$table->dropColumn('url');
		});
	}
}
