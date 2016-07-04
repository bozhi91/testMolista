<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoicingToSites extends Migration
{
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->text('invoicing')->after('signature');
		});

		// Populate invoicing field
		foreach (\App\Site::get() as $site)
		{
			$plan = $site->planchanges()->active()->first();
			if ( !$plan )
			{
				continue;
			}

			$site->invoicing = $plan['invoicing'];
			$site->save();
		}
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('invoicing');
		});
	}
}
