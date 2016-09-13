<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginToCustomersTable extends Migration
{
	public function up()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->string('origin')->default('web')->after('created_by');
		});
	}
	public function down()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->dropColumn('origin');
		});
	}
}
