<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMaxSpaceOnPlans extends Migration
{
	public function up()
	{
		\DB::statement("ALTER TABLE `plans` MODIFY `max_space` INT(11) NULL");
	}

	public function down()
	{
		\DB::statement("ALTER TABLE `plans` MODIFY `max_space` INT(11) NOT NULL");
	}
}
