<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePropertyPriceType extends Migration
{
	public function up()
	{
		\DB::statement('ALTER TABLE properties CHANGE COLUMN `price` `price` DOUBLE(15,2) NOT NULL AFTER `mode`');
	}

	public function down()
	{
		\DB::statement('ALTER TABLE properties CHANGE COLUMN `price` `price` DOUBLE(8,2) NOT NULL AFTER `mode`');
	}
}
