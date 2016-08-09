<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesUsersSignaturesTable extends Migration
{
	public function up()
	{
		Schema::create('sites_users_signatures', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned()->nullable()->index();
			$table->bigInteger('site_id')->unsigned()->nullable()->index();
			$table->string('title');
			$table->text('signature');
			$table->text('images');
			$table->boolean('default');
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('signature');
		});
	}

	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->boolean('signature')->default(1)->after('image');
		});

		Schema::drop('sites_users_signatures');
	}
}
