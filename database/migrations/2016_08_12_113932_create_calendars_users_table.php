<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsUsersTable extends Migration
{
	public function up()
	{
		Schema::create('calendars_users', function(Blueprint $table)
		{
			$table->bigInteger('calendar_id')->unsigned()->nullable()->index();
			$table->bigInteger('user_id')->unsigned()->nullable()->index();
			$table->primary(['calendar_id', 'user_id']);
			$table->foreign('calendar_id')->references('id')->on('calendars')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
		});

		// Populate calendars_users
		foreach (\App\Models\Calendar::withTrashed()->whereNotNull('user_id')->get() as $event)
		{
			$event->users()->sync([ $event->user_id ]);
		}

		// Modify calendars user_id field
		Schema::table('calendars', function(Blueprint $table)
		{
			$table->dropForeign(['user_id']);
			$table->dropColumn('user_id');
		});

	}

	public function down()
	{
		// Modify calendars user_id field
		Schema::table('calendars', function(Blueprint $table)
		{
			$table->bigInteger('user_id')->after('site_id')->unsigned()->nullable()->index();
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
		});

		// Populate calendars_users
		foreach (\App\Models\Calendar::withTrashed()->with('users')->get() as $event)
		{
			foreach ($event->users as $user)
			{
				$event->update([
					'user_id' => $user->id,
				]);
			}
		}

		// Drop calendars_users table
		Schema::drop('calendars_users');
	}
}
