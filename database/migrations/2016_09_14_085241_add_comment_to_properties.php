<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToProperties extends Migration
{
	public function up()
	{
		Schema::table('properties', function (Blueprint $table) {
			$table->text('comment')->nullable()->after('details');
		});
	}
	public function down()
	{
		Schema::table('properties', function (Blueprint $table) {
			$table->dropColumn('comment');
		});
	}
}
