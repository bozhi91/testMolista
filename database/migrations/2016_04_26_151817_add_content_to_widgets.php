<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentToWidgets extends Migration
{
	public function up()
	{
		Schema::table('widgets_translations', function(Blueprint $table)
		{
			$table->string('content')->after('title')->nullable();
		});
	}

	public function down()
	{
		Schema::table('widgets_translations', function(Blueprint $table)
		{
			$table->dropColumn('content');
		});
	}
}
