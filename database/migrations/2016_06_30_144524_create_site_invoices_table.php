<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteInvoicesTable extends Migration
{
	public function up()
	{
		Schema::create('sites_invoices', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->index();
			$table->string('title');
			$table->string('document');
			$table->float('amount');
			$table->timestamp('uploaded_at');
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('sites_invoices');
	}
}
