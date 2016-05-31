<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressPartsToProperties extends Migration
{
	public function up()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->text('address_parts')->after('address');
		});
	}

	public function down()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropColumn('address_parts');
		});
	}
}
