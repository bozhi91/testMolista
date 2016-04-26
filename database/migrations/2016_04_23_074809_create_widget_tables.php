<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetTables extends Migration
{
	public function up()
	{
		Schema::create('widgets', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->nullable()->unsigned()->index();
			$table->string('group');
			$table->string('type');
			$table->bigInteger('menu_id')->nullable()->unsigned()->index();
			$table->text('configuration');
			$table->integer('position');
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('menu_id')->references('id')->on('menus')->onUpdate('cascade')->onDelete('cascade');
		});
		Schema::create('widgets_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('widget_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('title')->nullable();

			$table->unique([ 'widget_id', 'locale' ]);
			$table->foreign('widget_id')->references('id')->on('widgets')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('widgets_translations');
		Schema::drop('widgets');
	}
}
