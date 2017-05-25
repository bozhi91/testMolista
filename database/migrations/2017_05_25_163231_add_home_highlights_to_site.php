<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHomeHighlightsToSite extends Migration
{

	public function up() {
		Schema::table('sites', function (Blueprint $table) {
			$table->integer('home_highlights')->default(3)->after('hide_molista');
		});
	}

	public function down() {
		Schema::table('sites', function (Blueprint $table) {
			$table->dropColumn('home_highlights');
		});
	}

}
