<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ColumnUpdatedatPropertyImages extends Migration {

	/**	
	 * @return void
	 */
	public function up() {
		Schema::table('properties_images', function (Blueprint $table) {
			$table->timestamps();
		});
	}

	/**	
	 * @return void
	 */
	public function down() {
		Schema::table('properties_images', function (Blueprint $table) {
			$table->dropTimestamps();
		});
	}

}
