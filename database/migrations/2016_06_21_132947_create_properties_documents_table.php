<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesDocumentsTable extends Migration
{
	public function up()
	{
		Schema::create('properties_documents', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('property_id')->unsigned()->index();
			$table->bigInteger('user_id')->unsigned()->nullable();
			$table->string('type')->index();
			$table->timestamp('date');
			$table->string('title');
			$table->string('description');
			$table->string('file');
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
		});
	}

	public function down()
	{
		Schema::drop('properties_documents');
	}
}
