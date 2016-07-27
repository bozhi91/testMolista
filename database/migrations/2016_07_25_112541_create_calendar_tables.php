<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarTables extends Migration
{
	public function up()
	{
		Schema::create('calendars', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned()->nullable()->index();
			$table->bigInteger('site_id')->unsigned()->nullable()->index();
			$table->bigInteger('property_id')->unsigned()->nullable()->index();
			$table->bigInteger('customer_id')->unsigned()->nullable()->index();
			$table->string('type')->nullable()->index();
			$table->string('status')->nullable()->index();
			$table->string('title')->nullable();
			$table->text('comments')->nullable();
			$table->text('location')->nullable();
			$table->text('data')->nullable();
			$table->timestamp('start_time')->index();
			$table->timestamp('end_time')->index();
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::table('sites', function(Blueprint $table)
		{
			$table->string('timezone')->nullable()->after('theme');
		});

		$timezones = [
			'ES' => 'Europe/Madrid',
			'AR' => 'America/Argentina/Buenos_Aires',
			'CL' => 'America/Santiago',
			'US' => 'America/New_York',
		];
		foreach (\App\Site::get() as $site)
		{
			$site->update([
				'timezone' => empty($timezones[$site->country_code]) ? $timezones['US'] : $timezones[$site->country_code],
			]);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('timezone');
		});

		Schema::drop('calendars');
	}
}
