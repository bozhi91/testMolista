<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersPropertiesTables extends Migration
{
	public function up()
	{
		Schema::create('customers_queries', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->bigInteger('customer_id')->unsigned();
			$table->string('mode');
			$table->string('type');
			$table->bigInteger('country_id')->nullable()->unsigned();
			$table->bigInteger('territory_id')->nullable()->unsigned();
			$table->bigInteger('state_id')->nullable()->unsigned();
			$table->bigInteger('city_id')->nullable()->unsigned();
			$table->string('zipcode');
			$table->string('district');
			$table->float('price_min')->nullable();
			$table->float('price_max')->nullable();
			$table->string('currency')->default('EUR');
			$table->float('size_min')->nullable();
			$table->float('size_max')->nullable();
			$table->string('size_unit')->default('sqm');
			$table->string('rooms')->nullable();
			$table->string('baths')->nullable();
			$table->text('more_attributes');
			$table->boolean('enabled')->index();
			$table->timestamps();
			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('territory_id')->references('id')->on('territories')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('set null');
		});
		Schema::create('properties_customers', function (Blueprint $table) 
		{
			$table->bigInteger('property_id')->unsigned();
			$table->bigInteger('customer_id')->unsigned();
			$table->primary([ 'property_id', 'customer_id' ]);
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('properties_customers');
		Schema::drop('customers_queries');
	}
}
