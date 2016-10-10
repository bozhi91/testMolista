<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsPropertiesTable extends Migration
{
	public function up()
	{
		Schema::create('calendars_properties', function(Blueprint $table)
		{
			$table->bigInteger('calendar_id')->unsigned()->nullable()->index();
			$table->bigInteger('property_id')->unsigned()->nullable()->index();
			$table->primary(['calendar_id', 'property_id']);
			$table->foreign('calendar_id')->references('id')->on('calendars')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
		});

		// Populate calendars_properties
		foreach (\App\Models\Calendar::withTrashed()->whereNotNull('property_id')->get() as $event)
		{
			$event->properties()->sync([ $event->property_id ]);
		}

		// Modify calendars property_id field
		Schema::table('calendars', function(Blueprint $table)
		{
			$table->dropForeign(['property_id']);
			$table->dropColumn('property_id');
		});

	}

	public function down()
	{
		// Modify calendars property_id field
		Schema::table('calendars', function(Blueprint $table)
		{
			$table->bigInteger('property_id')->after('site_id')->unsigned()->nullable()->index();
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
		});

		// Populate calendars property_id field
		foreach (\App\Models\Calendar::withTrashed()->with('properties')->get() as $event)
		{
			foreach ($event->properties as $property)
			{
				$event->update([
					'property_id' => $property->id,
				]);
			}
		}

		// Drop calendars_properties table
		Schema::drop('calendars_properties');
	}
}
