<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSignatureFieldsToUsers extends Migration
{
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->boolean('signature')->after('locale');
			$table->string('image')->after('locale');
			$table->string('linkedin')->after('locale');
			$table->string('phone')->after('locale');
		});
	}

	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('phone');
			$table->dropColumn('linkedin');
			$table->dropColumn('image');
			$table->dropColumn('signature');
		});
	}
}
