<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyTables extends Migration
{
	public function up()
	{
		Schema::create('services', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->boolean('enabled')->default(1)->index();
			$table->timestamps();
		});
		Schema::create('services_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('service_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('title');
			$table->string('slug')->index();
			$table->string('description');

			$table->unique([ 'service_id', 'locale' ]);
			$table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('properties', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->nullable()->unsigned()->index();
			$table->bigInteger('country_id')->nullable()->unsigned()->index();
			$table->bigInteger('territory_id')->nullable()->unsigned()->index();
			$table->bigInteger('state_id')->nullable()->unsigned()->index();
			$table->bigInteger('city_id')->nullable()->unsigned()->index();
			$table->string('ref')->index();
			$table->string('type')->index();
			$table->decimal('lat', 11, 8)->nullable();
			$table->decimal('lng', 11, 8)->nullable();
			$table->text('address');
			$table->string('zipcode');
			$table->string('district');
			$table->integer('rooms')->index();
			$table->integer('baths')->index();
			$table->float('size')->index();
			$table->string('size_unit')->default('sqm');
			$table->string('mode')->index();
			$table->float('price')->index();
			$table->string('currency')->default('EUR');
			$table->boolean('enabled')->default(1)->index();
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('territory_id')->references('id')->on('territories')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('set null');
		});
		Schema::create('properties_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('property_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('title');
			$table->string('slug')->index();
			$table->text('description');
			$table->unique([ 'property_id', 'locale' ]);
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('properties_images', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('property_id')->unsigned()->index();
			$table->string('image');
			$table->integer('position');
			$table->boolean('default')->index();
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('properties_services', function (Blueprint $table) 
		{
			$table->bigInteger('property_id')->unsigned();
			$table->bigInteger('service_id')->unsigned();
			$table->primary([ 'property_id', 'service_id' ]);
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('properties_users', function (Blueprint $table) 
		{
			$table->bigInteger('property_id')->unsigned();
			$table->bigInteger('user_id')->unsigned();
			$table->boolean('is_owner')->index();
			$table->primary([ 'property_id', 'user_id' ]);
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('properties_users');
		Schema::drop('properties_services');
		Schema::drop('properties_images');
		Schema::drop('properties_translations');
		Schema::drop('properties');
		Schema::drop('services_translations');
		Schema::drop('services');
	}
}
