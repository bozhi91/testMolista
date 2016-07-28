<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarketplaceExportAllToSitesMarketplaces extends Migration
{
	public function up()
	{
		Schema::table('sites_marketplaces', function(Blueprint $table)
		{
			$table->boolean('marketplace_export_all');
		});
	}

	public function down()
	{
		Schema::table('sites_marketplaces', function(Blueprint $table)
		{
			$table->dropColumn('marketplace_export_all');
		});
	}
}
