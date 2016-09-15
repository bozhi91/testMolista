<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHideLogoToSites extends Migration
{
	public function up()
	{
		Schema::table('sites', function (Blueprint $table) {
			$table->boolean('hide_molista')->after('customer_register');
		});
	}
	public function down()
	{
		Schema::table('sites', function (Blueprint $table) {
			$table->dropColumn('hide_molista');
		});
	}
}
