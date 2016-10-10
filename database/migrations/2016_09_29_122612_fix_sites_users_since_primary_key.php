<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSitesUsersSincePrimaryKey extends Migration
{
	public function up()
	{
		Schema::table('sites_users_since', function (Blueprint $table) {
			$table->dropUnique('user_id');
			$table->unique([ 'site_id', 'user_id', 'section' ]);
		});
	}

	public function down()
	{
		Schema::table('sites_users_since', function (Blueprint $table) {
			$table->dropUnique([ 'site_id', 'user_id', 'section' ]);
			$table->unique('site_id','user_id');
		});
	}
}
