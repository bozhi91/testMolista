<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGeonameidToGeographyTables extends Migration
{
	public function up()
	{
		Schema::table('countries', function(Blueprint $table)
		{
			$table->bigInteger('geonameid')->nullable()->after('id');
		});
		Schema::table('territories', function(Blueprint $table)
		{
			$table->bigInteger('geonameid')->nullable()->after('id');
			$table->dropUnique(['code']);
			$table->unique(['country_id','code']);
		});
		Schema::table('states', function(Blueprint $table)
		{
			$table->bigInteger('geonameid')->nullable()->after('id');
			$table->dropUnique(['code']);
			$table->unique(['country_id','code']);
		});
		Schema::table('cities', function(Blueprint $table)
		{
			$table->bigInteger('geonameid')->nullable()->after('id');
		});
	}

	public function down()
	{
		Schema::table('cities', function(Blueprint $table)
		{
			$table->dropColumn('geonameid');
		});
		Schema::table('states', function(Blueprint $table)
		{
			$table->dropColumn('geonameid');
		});
		Schema::table('territories', function(Blueprint $table)
		{
			$table->dropColumn('geonameid');
		});
		Schema::table('countries', function(Blueprint $table)
		{
			$table->dropColumn('geonameid');
		});
	}
}