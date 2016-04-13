<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationTables extends Migration
{
	public function up()
	{
		// Main translations table
		Schema::create('translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('file', 255)->index();
			$table->string('tag', 255)->index();
			$table->timestamps();

			// Unique key
			$table->unique([ 'file', 'tag' ]);
		});

		// Locale translations table
		Schema::create('translations_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('translation_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->text('value');

			$table->unique([ 'translation_id', 'locale' ]);
			$table->foreign('translation_id')->references('id')->on('translations')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});

		// User translation locales relation table
		Schema::create('user_translation_locales', function (Blueprint $table) {
			$table->bigInteger('user_id')->unsigned();
			$table->integer('locale_id')->unsigned();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale_id')->references('id')->on('locales')->onUpdate('cascade')->onDelete('cascade');

			$table->primary(['user_id', 'locale_id']);
		});
	}

	public function down()
	{
		Schema::drop('translations_translations');
		Schema::drop('translations');
		Schema::drop('user_translation_locales');
	}
}
