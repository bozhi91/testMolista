<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParsedTable extends Migration
{
	public function up()
	{
		Schema::create('parse_requests', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('service')->index();
			$table->string('query')->index();
			$table->integer('last_page');
			$table->timestamps();
			$table->timestamp('finished_at')->nullable();
			$table->unique([ 'service', 'query' ]);
		});
		Schema::create('parse_requests_items', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('parse_request_id')->nullable()->unsigned()->index();
			$table->string('service_id')->index();
			$table->text('columns');
			$table->timestamps();
			$table->unique([ 'parse_request_id', 'service_id' ]);
			$table->foreign('parse_request_id')->references('id')->on('parse_requests')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('parse_requests_items');
		Schema::drop('parse_requests');
	}
}
