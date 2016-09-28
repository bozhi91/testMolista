<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommissionFixedToPropertiesCatches extends Migration
{
	public function up()
	{
		Schema::table('properties_catches', function (Blueprint $table) {
			$table->float('commission_fixed')->after('commission');
		});
	}
	public function down()
	{
		Schema::table('properties_catches', function (Blueprint $table) {
			$table->dropColumn('commission_fixed');
		});
	}
}
