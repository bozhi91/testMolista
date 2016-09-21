<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesImportsTable extends Migration
{
	public function up()
	{
		Schema::create('sites_imports', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->nullable()->index();
			$table->string('version', 10);
			$table->string('filename');
			$table->string('status', 40);
			$table->text('result');
			$table->nullableTimestamps();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('sites_imports');
	}
}
