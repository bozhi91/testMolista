<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebTransferRequestedToSites extends Migration
{
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->boolean('web_transfer_requested');
		});
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('web_transfer_requested');
		});
	}
}
