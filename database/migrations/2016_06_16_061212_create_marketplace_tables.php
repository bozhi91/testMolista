<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketplaceTables extends Migration
{
	public function up()
	{
		Schema::create('marketplaces', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('code')->index()->unique();
			$table->string('class_path');
			$table->bigInteger('country_id')->nullable()->unsigned()->index();
			$table->string('name');
			$table->string('logo');
			$table->text('configuration');
			$table->boolean('enabled');
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('set null');
		});
		Schema::create('marketplaces_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('marketplace_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('instructions')->nullable();
			$table->unique([ 'marketplace_id', 'locale' ]);
			$table->foreign('marketplace_id')->references('id')->on('marketplaces')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});
		Schema::create('properties_marketplaces', function (Blueprint $table) {
			$table->bigInteger('property_id')->unsigned();
			$table->bigInteger('marketplace_id')->unsigned();
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('marketplace_id')->references('id')->on('marketplaces')->onUpdate('cascade')->onDelete('cascade');
			$table->primary(['property_id', 'marketplace_id']);
		});
		Schema::create('sites_marketplaces', function (Blueprint $table) {
			$table->bigInteger('site_id')->unsigned();
			$table->bigInteger('marketplace_id')->unsigned();
			$table->text('marketplace_configuration');
			$table->boolean('marketplace_enabled');
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('marketplace_id')->references('id')->on('marketplaces')->onUpdate('cascade')->onDelete('cascade');
			$table->primary(['site_id', 'marketplace_id']);
		});
	}

	public function down()
	{
		Schema::drop('sites_marketplaces');
		Schema::drop('properties_marketplaces');
		Schema::drop('marketplaces_translations');
		Schema::drop('marketplaces');
	}
}
