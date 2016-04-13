<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocaleTables extends Migration
{
	public function up()
	{
		// Create locale table
		Schema::create('locales', function (Blueprint $table) {
			$table->increments('id');
			$table->char('locale', 2)->index()->unique();
			$table->string('flag');
			$table->char('dir', 3)->default('ltr');
			$table->string('name');
			$table->string('script');
			$table->string('native');
			$table->string('regional');
			$table->boolean('web')->default(0);
			$table->boolean('admin')->default(1);
			$table->timestamps();
		});

		// Add default languages
		\DB::table('locales')->insert([
			'locale' => 'en',
			'name' => 'English',
			'script' => 'Latn',
			'native' => 'English',
			'regional' => 'en_GB',
			'web' => 1,
			'admin' => 1,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
		\DB::table('locales')->insert([
			'locale' => 'es',
			'name' => 'Spanish',
			'script' => 'Latn',
			'native' => 'Español',
			'regional' => 'es_ES',
			'web' => 1,
			'admin' => 1,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);


		Schema::table('users', function(Blueprint $table) {
			// Add locale field to users
			$table->char('locale', 2)->after('email')->index()->nullable()->default('en');
			// Add foreign key to users
			$table->foreign('locale','users_locale_foreign')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('set null');
		});
	}

	public function down()
	{
		Schema::table('users', function(Blueprint $table) {
			// Drop user local foreign key
			$table->dropForeign('users_locale_foreign');
			// Drop locale field from users
			$table->dropColumn('locale');
		});

		// Drop table locales
		Schema::drop('locales');
	}
}
