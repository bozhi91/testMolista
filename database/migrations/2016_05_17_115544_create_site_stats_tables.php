<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteStatsTables extends Migration
{
	public function up()
	{
		Schema::create('sites_stats', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->date('date')->index();
			$table->bigInteger('site_id')->unsigned()->index();
			$table->integer('sale')->comment('Propiedades publicadas para vender');
			$table->integer('rent')->comment('Propiedades publicadas para alquilar');
			$table->double('sale_price',15,2)->comment('Precio promedio de venta propiedades');
			$table->double('rent_price',15,2)->comment('Precio promedio de alquiler propiedades');
			$table->float('sale_sqm')->comment('Superficie promedio en venta');
			$table->float('rent_sqm')->comment('Superficie promedio en alquiler');
			$table->integer('sale_closed')->comment('Propiedades vendidas');
			$table->integer('rent_closed')->comment('Propiedades alquiladas');
			$table->integer('sale_visits')->comment('Visitas realizadas para venta');
			$table->integer('rent_visits')->comment('Visitas realizadas para alquiler');
			$table->integer('leads')->comment('Leads creados');
			$table->integer('current_sale')->comment('Propiedades publicadas para vender hasta esta fecha');
			$table->integer('current_rent')->comment('Propiedades publicadas para alquilar hasta esta fecha');
			$table->double('current_sale_price',15,2)->comment('Precio promedio de venta propiedades hasta esta fecha');
			$table->double('current_rent_price',15,2)->comment('Precio promedio de alquiler propiedades hasta esta fecha');
			$table->float('current_sale_sqm')->comment('Superficie promedio en venta');
			$table->float('current_rent_sqm')->comment('Superficie promedio en alquiler');

			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('users_stats', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->date('date')->index();
			$table->bigInteger('site_id')->unsigned()->index();
			$table->bigInteger('user_id')->unsigned()->index();
			$table->integer('sale')->comment('Propiedades publicadas para vender');
			$table->integer('rent')->comment('Propiedades publicadas para alquilar');
			$table->integer('sale_visits')->comment('Visitas realizadas para venta');
			$table->integer('rent_visits')->comment('Visitas realizadas para alquiler');
			$table->integer('sale_closed')->comment('Propiedades vendidas');
			$table->integer('rent_closed')->comment('Propiedades alquiladas');

			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::table('properties', function(Blueprint $table)
		{
			$table->date('published_at')->after('ec_pending')->index();
			$table->bigInteger('publisher_id')->nullable()->unsigned()->after('ec_pending')->index();
			$table->foreign('publisher_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
		});

		\DB::update("UPDATE properties SET `published_at` = DATE(`created_at`)");
	}

	public function down()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropForeign([ 'publisher_id' ]);
			$table->dropColumn('published_at');
			$table->dropColumn('publisher_id');
		});
		Schema::drop('users_stats');
		Schema::drop('sites_stats');
	}
}
