<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApikeysTable extends Migration
{
	public function up()
	{
		Schema::create('apikeys', function(Blueprint $table)
		{
		$table->bigIncrements('id');
		$table->bigInteger('site_id')->nullable()->unsigned()->index();
		$table->string('key', 40)->unique();
		$table->string('name');
		$table->bigInteger('created_by')->nullable()->unsigned()->index();
		$table->timestamps();
		$table->softDeletes();

		$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('set null');
		$table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
		});
	}

	public function down()
	{
		Schema::drop('apikeys');
	}
}
