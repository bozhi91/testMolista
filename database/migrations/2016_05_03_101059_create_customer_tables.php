<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTables extends Migration
{
	public function up()
	{
		Schema::create('customers', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->nullable()->unsigned()->index();
			$table->bigInteger('created_by')->nullable()->unsigned()->index();
			$table->char('locale', 2)->nullable()->default('en');
			$table->string('first_name');
			$table->string('last_name');
			$table->string('email')->index();
			$table->string('password');
			$table->string('phone');
			$table->rememberToken();
			$table->boolean('validated');
			$table->timestamps();

			$table->unique([ 'site_id', 'email' ]);
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('set null');
		});

		Schema::table('sites', function(Blueprint $table)
		{
			$table->boolean('customer_register')->after('custom_theme')->default(1);
		});
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('customer_register');
		});

		Schema::drop('customers');
	}}
