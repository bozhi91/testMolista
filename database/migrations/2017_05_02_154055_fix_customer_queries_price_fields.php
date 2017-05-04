<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCustomerQueriesPriceFields extends Migration
{

	public function up()
	{
		\DB::statement('ALTER TABLE customers_queries CHANGE COLUMN `price_min` `price_min` DOUBLE(15,2) NOT NULL AFTER `district`');
		\DB::statement('ALTER TABLE customers_queries CHANGE COLUMN `price_max` `price_max` DOUBLE(15,2) NOT NULL AFTER `price_min`');
	}

	public function down()
	{
		\DB::statement('ALTER TABLE customers_queries CHANGE COLUMN `price_max` `price_max` DOUBLE(8,2) NOT NULL AFTER `price_min`');
		\DB::statement('ALTER TABLE customers_queries CHANGE COLUMN `price_min` `price_min` DOUBLE(8,2) NOT NULL AFTER `district`');
	}

}
