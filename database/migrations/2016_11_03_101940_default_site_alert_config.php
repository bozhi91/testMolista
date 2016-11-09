<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DefaultSiteAlertConfig extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('sites', function (Blueprint $table) {
			$table->binary('alert_config')->nullable()->change();
		});
		
		\DB::table('sites')->update(['alert_config' => null]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('sites', function (Blueprint $table) {
			$table->binary('alert_config')->change();
		});
	}

}
