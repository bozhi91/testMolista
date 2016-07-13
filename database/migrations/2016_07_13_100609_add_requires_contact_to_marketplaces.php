<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequiresContactToMarketplaces extends Migration
{
	public function up()
	{
		Schema::table('marketplaces', function(Blueprint $table)
		{
			$table->boolean('requires_contact')->after('configuration');
		});
	}

	public function down()
	{
		Schema::table('marketplaces', function(Blueprint $table)
		{
			$table->dropColumn('requires_contact');
		});
	}
}
