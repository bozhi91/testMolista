<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyCathTables extends Migration
{
	public function up()
	{
		Schema::create('properties_catches', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('property_id')->unsigned()->nullable()->index();
			$table->bigInteger('employee_id')->unsigned()->nullable()->index();
			$table->bigInteger('buyer_id')->unsigned()->nullable()->index();
			$table->bigInteger('closer_id')->unsigned()->nullable()->index();
			$table->dateTime('catch_date')->index();
			$table->dateTime('transaction_date')->nullable()->index();;
			$table->string('seller_first_name');
			$table->string('seller_last_name');
			$table->string('seller_email');
			$table->string('seller_phone');
			$table->string('seller_cell');
			$table->string('seller_id_card');
			$table->double('price_original',15,2);
			$table->double('price_min',15,2);
			$table->double('price_sold',15,2)->nullable();
			$table->float('commission');
			$table->string('status');
			$table->text('reason');
			$table->integer('leads_to_close')->nullable();
			$table->float('leads_average')->nullable();
			$table->float('discount_to_close')->nullable();
			$table->float('discount_average')->nullable();
			$table->integer('days_to_close')->nullable();
			$table->float('days_average')->nullable();
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('employee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('buyer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('closer_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
		});
	}

	public function down()
	{
		Schema::drop('properties_catches');
	}
}
