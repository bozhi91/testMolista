<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteTables extends Migration
{
	public function up()
	{
		Schema::create('sites', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('subdomain')->unique()->index();
			$table->string('theme');
			$table->string('logo');
			$table->string('favicon');
			$table->boolean('enabled')->default(1)->index();
			$table->timestamps();
		});

		Schema::create('sites_locales', function (Blueprint $table) {
			$table->bigInteger('site_id')->unsigned();
			$table->integer('locale_id')->unsigned();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale_id')->references('id')->on('locales')->onUpdate('cascade')->onDelete('cascade');
			$table->primary(['site_id', 'locale_id']);
		});

		Schema::create('sites_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('title')->nullable();
			$table->string('subtitle')->nullable();
			$table->string('description')->nullable();
			$table->unique([ 'site_id', 'locale' ]);
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('sites_domains', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned();
			$table->string('domain')->unique()->index();
			$table->boolean('default')->default(0)->index();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('sites_users', function (Blueprint $table) {
			$table->bigInteger('site_id')->unsigned();
			$table->bigInteger('user_id')->unsigned();
			$table->boolean('can_create')->default(1);
			$table->boolean('can_edit')->default(1);
			$table->boolean('can_delete')->default(1);
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
			$table->primary(['site_id', 'user_id']);
		});
	}

	public function down()
	{
		Schema::drop('sites_users');
		Schema::drop('sites_domains');
		Schema::drop('sites_translations');
		Schema::drop('sites_locales');
		Schema::drop('sites');
	}
}
