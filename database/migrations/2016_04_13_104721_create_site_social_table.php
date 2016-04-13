<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteSocialTable extends Migration
{
	public function up()
	{
		Schema::create('sites_social', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned();
			$table->string('network');
			$table->text('url');
			$table->boolean('enabled')->default(1)->index();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('sites_social');
	}
}
