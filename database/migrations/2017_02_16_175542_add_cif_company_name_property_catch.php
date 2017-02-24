<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCifCompanyNamePropertyCatch extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('properties_catches', function (Blueprint $table) {
			$table->string('company_name')->after('seller_id_card');
			$table->string('cif')->after('company_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('properties_catches', function (Blueprint $table) {
			$table->dropColumn('company_name');
			$table->dropColumn('cif');
		});
	}

}
