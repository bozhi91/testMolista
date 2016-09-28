<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalStatsTable extends Migration
{
	public function up()
	{
		Schema::create('stats', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->nullable()->index();
			$table->bigInteger('plan_id')->unsigned()->nullable()->index();
			$table->integer('plan_level')->unsigned()->nullable()->index();
			$table->string('payment_interval')->nullable();
			$table->string('payment_method')->nullable();
			$table->float('monthly_fee');
			$table->date('date_created');
			$table->text('address');
			$table->text('infowindow');
			$table->decimal('lat', 11, 8)->nullable();
			$table->decimal('lng', 11, 8)->nullable();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('stats');
	}
}
