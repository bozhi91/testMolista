<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnDataOnWidgets extends Migration {

	/** 	 
	 * @return void
	 */
	public function up() {
		Schema::table('widgets', function($table) {
			$table->binary('data');
		});
	}

	/**	 *
	 * @return void
	 */
	public function down() {
		$table->dropColumn('data');
	}

}
