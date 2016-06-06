<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTables extends Migration
{
	public function up()
	{
		Schema::create('plans', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->string('code')->unique()->index();
			$table->string('name');
			$table->boolean('is_free');
			$table->float('price_year')->nullable();;
			$table->float('price_month')->nullable();;
			$table->integer('max_employees')->nullable();
			$table->integer('max_space');
			$table->integer('max_properties')->nullable();
			$table->integer('max_languages')->nullable();
			$table->text('configuration');
			$table->text('extras');
			$table->string('stripe_month_id')->nullable();
			$table->string('stripe_year_id')->nullable();
			$table->boolean('enabled')->index();
			$table->timestamps();
		});
		Schema::table('sites', function(Blueprint $table)
		{
			$table->bigInteger('plan_id')->unsigned()->nullable();
			$table->string('stripe_id')->nullable()->index();
			$table->dateTime('paid_until')->nullable();
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('set null');
		});
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropForeign(['plan_id']);
			$table->dropColumn('paid_until');
			$table->dropColumn('stripe_id');
			$table->dropColumn('plan_id');
		});
		Schema::drop('plans');
	}
}
