<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarketplaceMaxpropertiesToSitesMarketplaces extends Migration
{
	public function up()
	{
		Schema::table('sites_marketplaces', function(Blueprint $table)
		{
			$table->integer('marketplace_maxproperties')->nullable();
		});
	}

	public function down()
	{
		Schema::table('sites_marketplaces', function(Blueprint $table)
		{
			$table->dropColumn('marketplace_maxproperties');
		});
	}
}
