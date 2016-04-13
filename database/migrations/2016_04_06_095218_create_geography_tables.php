<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeographyTables extends Migration
{
	public function up()
	{
		// Countries
		Schema::create('countries', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('code', 2)->index();
			$table->boolean('enabled')->default(1)->index();
			$table->timestamps();
			$table->unique([ 'code' ]);
		});
		Schema::create('countries_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('country_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('name');

			$table->unique([ 'country_id', 'locale' ]);
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});

		// Territories
		Schema::create('territories', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('country_id')->unsigned()->index();
			$table->string('code')->index();
			$table->boolean('enabled')->default(1)->index();
			$table->string('name');
			$table->string('slug')->nullable()->index();
			$table->timestamps();

			$table->unique([ 'code' ]);
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
		});

		// States
		Schema::create('states', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('country_id')->unsigned()->index();
			$table->bigInteger('territory_id')->unsigned()->nullable()->index();
			$table->string('code')->index();
			$table->boolean('enabled')->default(1)->index();
			$table->string('name');
			$table->string('slug')->nullable()->index();
			$table->timestamps();

			$table->unique([ 'code' ]);
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('territory_id')->references('id')->on('territories')->onUpdate('cascade')->onDelete('set null');
		});
		
		// Cities
		Schema::create('cities', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('state_id')->unsigned()->index();
			$table->boolean('enabled')->default(1)->index();
			$table->string('name')->index();
			$table->string('slug')->nullable()->index();
			$table->timestamps();

			$table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('cities');
		Schema::drop('states');
		Schema::drop('territories');
		Schema::drop('countries_translations');
		Schema::drop('countries');
	}
}
