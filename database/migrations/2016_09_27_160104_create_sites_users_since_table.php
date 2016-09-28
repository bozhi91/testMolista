<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesUsersSinceTable extends Migration
{
	public function up()
	{
		Schema::create('sites_users_since', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->nullable()->index();
			$table->bigInteger('user_id')->unsigned()->nullable()->index();
			$table->string('section');
			$table->date('since')->nullable();
			$table->nullableTimestamps();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
			$table->unique('site_id', 'user_id', 'section');	
		});
	}

	public function down()
	{
		Schema::drop('sites_users_since');
	}
}
