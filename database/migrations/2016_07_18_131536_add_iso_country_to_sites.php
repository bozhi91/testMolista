<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsoCountryToSites extends Migration
{
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->string('country_code',2)->after('id')->nullable();
			$table->foreign('country_code')->references('code')->on('countries')->onUpdate('cascade')->onDelete('set null');
		});
		\DB::statement("UPDATE `sites` SET `country_code`='ES' WHERE 1");
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropForeign(['country_code']);
			$table->dropColumn('country_code');
		});
	}
}
