<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitePricerangeTables extends Migration
{
	public function up()
	{
		Schema::create('sites_priceranges', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->index();
			$table->string('type')->index();
			$table->integer('from')->nullable();
			$table->integer('till')->nullable();
			$table->integer('position');
			$table->boolean('enabled')->default(1);
			$table->timestamps();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});
		Schema::create('sites_priceranges_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_pricerange_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('title')->nullable();
			$table->unique([ 'site_pricerange_id', 'locale' ]);
			$table->foreign('site_pricerange_id')->references('id')->on('sites_priceranges')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('sites_priceranges_translations');
		Schema::drop('sites_priceranges');
	}
}
