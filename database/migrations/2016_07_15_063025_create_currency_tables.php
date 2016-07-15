<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyTables extends Migration
{
	public function up()
	{
		// Currencies
		Schema::create('currencies', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('code',3)->unique();
			$table->string('symbol');
			$table->integer('decimals')->default(2);
			$table->string('position')->default('before');
			$table->boolean('enabled')->default(1);
			$table->timestamps();
			$table->softDeletes();
		});
		// Currencies translations
		Schema::create('currencies_translations', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('currency_id')->unsigned()->index();
			$table->string('locale', 2)->index();
			$table->string('title')->nullable();
			$table->unique([ 'currency_id', 'locale' ]);
			$table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('cascade');
		});
		// Basic currencies
		\App\Models\Currency::saveModel([
			'code' => 'USD',
			'symbol' => '$',
			'decimals' => 0,
			'position' => 'before',
			'enabled' => 1,
			'i18n' => [
				'title' => [
					'es' => 'Dólar',
					'en' => 'Dollar',
				],
			],
		]);
		\App\Models\Currency::saveModel([
			'code' => 'EUR',
			'symbol' => '€',
			'decimals' => 0,
			'position' => 'after',
			'enabled' => 1,
			'i18n' => [
				'title' => [
					'es' => 'Euro',
					'en' => 'Euro',
				],
			],
		]);
		// Add currency to countries
		Schema::table('countries', function(Blueprint $table)
		{
			$table->string('currency',3)->nullable()->after('code');
			$table->foreign('currency')->references('code')->on('currencies')->onUpdate('cascade')->onDelete('set null');
		});
		\DB::statement("UPDATE `countries` SET `currency`='USD' WHERE 1");
		\DB::statement("UPDATE `countries` SET `currency`='EUR' WHERE `code` IN ('AL','AD','AT','BY','BE','BA','BG','HR','CY','CZ','DK','EE','FO','FI','FR','DE','GI','GR','HU','IS','IE','IT','LV','LI','LT','LU','MK','MT','MD','MC','NL','NO','PL','PT','RO','RU','SM','RS','SK','SI','ES','SE','CH','UA','GB','VA','RS','IM','RS','ME')");
		// Add currency to plans
		Schema::table('plans', function(Blueprint $table)
		{
			$table->string('currency',3)->nullable()->after('is_free');
			$table->foreign('currency')->references('code')->on('currencies')->onUpdate('cascade')->onDelete('cascade');
		});
		\DB::statement("UPDATE `plans` SET `currency`='EUR' WHERE `is_free`=0");
		// Modify currency type on properties
		\DB::statement("ALTER TABLE `properties` MODIFY `currency` VARCHAR(3) NULL");
		// Index currency on properties
		Schema::table('properties', function(Blueprint $table)
		{
			$table->foreign('currency')->references('code')->on('currencies')->onUpdate('cascade')->onDelete('set null');
		});
		// Add currencies to sites
		Schema::table('sites', function(Blueprint $table)
		{
			$table->string('site_currency',3)->nullable()->after('country_id');
			$table->string('payment_currency',3)->nullable()->after('country_id');
			$table->foreign('site_currency')->references('code')->on('currencies')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('payment_currency')->references('code')->on('currencies')->onUpdate('cascade')->onDelete('set null');
		});
		\DB::statement("UPDATE `sites` SET `site_currency`='EUR', `payment_currency`='EUR' WHERE 1");
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropForeign(['payment_currency']);
			$table->dropForeign(['site_currency']);
			$table->dropColumn('payment_currency');
			$table->dropColumn('site_currency');
		});
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropForeign(['currency']);
		});
		\DB::statement("ALTER TABLE `properties` MODIFY `currency` VARCHAR(255)");
		Schema::table('plans', function(Blueprint $table)
		{
			$table->dropForeign(['currency']);
			$table->dropColumn('currency');
		});
		Schema::table('countries', function(Blueprint $table)
		{
			$table->dropForeign(['currency']);
			$table->dropColumn('currency');
		});
		Schema::drop('currencies_translations');
		Schema::drop('currencies');
	}
}
