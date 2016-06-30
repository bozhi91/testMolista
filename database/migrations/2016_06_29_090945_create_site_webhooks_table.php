<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteWebhooksTable extends Migration
{
	public function up()
	{
		Schema::create('sites_webhooks', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->index();
			$table->string('source');
			$table->string('event');
			$table->text('data');
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('sites_webhooks');
	}
}
