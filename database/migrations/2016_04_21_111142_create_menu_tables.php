<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTables extends Migration
{
	public function up()
	{
		Schema::create('menus', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->nullable()->unsigned()->index();
			$table->string('title');
			$table->string('slug')->index();
			$table->boolean('enabled')->default(1)->index();
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('menus_items', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('menu_id')->unsigned()->index();
			$table->bigInteger('page_id')->unsigned()->index()->nullable();
			$table->bigInteger('property_id')->unsigned()->index()->nullable();
			$table->string('type');
			$table->string('target');
			$table->integer('position');

			$table->foreign('menu_id')->references('id')->on('menus')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('page_id')->references('id')->on('pages')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('property_id')->references('id')->on('properties')->onUpdate('cascade')->onDelete('cascade');
		});
		Schema::create('menus_items_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('menu_item_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('title')->nullable();
			$table->string('url')->nullable();

			$table->unique([ 'menu_item_id', 'locale' ]);
			$table->foreign('menu_item_id')->references('id')->on('menus_items')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('menus_items_translations');
		Schema::drop('menus_items');
		Schema::drop('menus');
	}
}
