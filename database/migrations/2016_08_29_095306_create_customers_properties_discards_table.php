<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersPropertiesDiscardsTable extends Migration
{
	public function up()
	{
		Schema::create('properties_customers_discards', function (Blueprint $table) 
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
		Schema::drop('properties_customers_discards');
	}
}
