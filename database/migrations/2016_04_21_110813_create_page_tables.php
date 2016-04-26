<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageTables extends Migration
{
	public function up()
	{
		Schema::create('pages', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->nullable()->unsigned()->index();
			$table->boolean('enabled')->default(1)->index();
			$table->timestamps();

			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});
		Schema::create('pages_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('page_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('title')->nullable();
			$table->string('slug')->nullable()->index();
			$table->text('body')->nullable();
			$table->string('seo_title')->nullable();
			$table->string('seo_description')->nullable();
			$table->string('seo_keywords')->nullable();

			$table->unique([ 'page_id', 'locale' ]);
			$table->foreign('page_id')->references('id')->on('pages')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::drop('pages_translations');
		Schema::drop('pages');
	}
}
