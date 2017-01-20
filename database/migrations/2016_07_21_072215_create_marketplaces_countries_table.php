<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketplacesCountriesTable extends Migration
{
	public function up()
	{
		Schema::create('marketplaces_countries', function (Blueprint $table) {
			$table->bigInteger('marketplace_id')->unsigned();
			$table->bigInteger('country_id')->unsigned();
			$table->foreign('marketplace_id')->references('id')->on('marketplaces')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
			$table->primary(['marketplace_id','country_id']);
		});

		foreach (\App\Models\Marketplace::get() as $marketplace)
		{
			$marketplace->countries()->attach($marketplace->country_id);
		}

		Schema::table('marketplaces', function(Blueprint $table)
		{
			$table->dropForeign(['country_id']);
			$table->dropColumn('country_id');
		});
	}

	public function down()
	{
		Schema::drop('marketplaces_countries');

		Schema::table('marketplaces', function(Blueprint $table)
		{
			$table->bigInteger('country_id')->after('class_path')->unsigned()->nullable();
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('set null');
		});

		$spain = \App\Models\Geography\Country::where('code','ES')->first();
		if ( $spain )
		{
			\DB::statement("UPDATE `marketplaces` SET `country_id`={$spain->id} WHERE 1");
		}
	}
}
