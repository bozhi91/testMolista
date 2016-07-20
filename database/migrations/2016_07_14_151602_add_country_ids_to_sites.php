<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryIdsToSites extends Migration
{
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->bigInteger('country_id')->unsigned()->nullable()->after('mailer');
			$table->text('country_ids')->nullable()->after('mailer');
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('set null');
		});

		\DB::statement('UPDATE `countries` SET `enabled`=1 WHERE 1');
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropForeign(['country_id']);
			$table->dropColumn('country_id');
			$table->dropColumn('country_ids');
		});
	}
}
