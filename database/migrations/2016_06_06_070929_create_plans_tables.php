<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTables extends Migration
{
	public function up()
	{
		Schema::create('plans', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->string('code')->unique()->index();
			$table->string('name');
			$table->boolean('is_free');
			$table->float('price_year')->nullable();;
			$table->float('price_month')->nullable();;
			$table->integer('max_employees')->nullable();
			$table->integer('max_space');
			$table->integer('max_properties')->nullable();
			$table->integer('max_languages')->nullable();
			$table->text('configuration');
			$table->text('extras');
			$table->string('stripe_month_id')->nullable();
			$table->string('stripe_year_id')->nullable();
			$table->integer('level');
			$table->boolean('enabled')->index();
			$table->timestamps();
		});
		Schema::table('sites', function(Blueprint $table)
		{
			$table->bigInteger('plan_id')->unsigned()->nullable();
			$table->string('payment_interval')->nullable();
			$table->string('payment_method')->nullable();
			$table->string('iban_account')->nullable();
			$table->string('stripe_id')->nullable()->index();
			$table->dateTime('paid_until')->nullable();
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('set null');
		});

		// Create plans
		$plan_defaults = [
			'enabled' => 1,
			'level' => 0,
			'code' => false,
			'name' => false,
			'is_free' => 0,
			'price_year' => 0,
			'stripe_year_id' => '',
			'price_month' => 0,
			'stripe_month_id' => '',
			'max_employees' => '',
			'max_properties' => '',
			'max_languages' => '',
			'max_space' => '',
		];
		$plan_configuration = [
			'support_email' => 1,
			'support_phone' => 1,
			'qr' => 1,
			'printing' => 1,
			'integrations' => 1,
			'reporting' => 1,
			'analytics' => 1,
			'responsive' => 1,
			'filters' => 1,
			'leads' => 1,
			'crm' => 1,
			'logs' => 1,
			'widgets' => 1,
		];
		$plan_extras = [
			'transfer' => 299,
		];
		// Free
		\App\Models\Plan::saveModel(array_merge($plan_defaults, [
			'level' => 0,
			'code' => 'free',
			'name' => 'Free',
			'is_free' => 1,
			'max_employees' => 1,
			'max_properties' => 20,
			'max_languages' => 1,
			'max_space' => 1,
			'configuration' => array_merge($plan_configuration, [
				'support_email' => 0,
				'support_phone' => 0,
				'qr' => 0,
				'printing' => 0,
				'analytics' => 0,
			]),
			'extras' => array_merge($plan_extras, [
			]),
		]));
		// Pro
		\App\Models\Plan::saveModel(array_merge($plan_defaults, [
			'level' => 1,
			'code' => 'pro',
			'name' => 'Pro',
			'price_year' => 259,
			'stripe_year_id' => 'MOL PRO Y',
			'price_month' => 29,
			'stripe_month_id' => 'MOL PRO M',
			'max_employees' => 5,
			'max_properties' => 250,
			'max_space' => 4,
			'configuration' => array_merge($plan_configuration, [
				'support_phone' => 0,
			]),
			'extras' => array_merge($plan_extras, [
			]),
		]));
		// Plus
		\App\Models\Plan::saveModel(array_merge($plan_defaults, [
			'level' => 2,
			'code' => 'plus',
			'name' => 'Plus',
			'price_year' => 599,
			'stripe_year_id' => 'MOL PLUS Y',
			'price_month' => 59,
			'stripe_month_id' => 'MOL PLUS M',
			'max_space' => 8,
			'configuration' => array_merge($plan_configuration, [
			]),
			'extras' => array_merge($plan_extras, [
				'transfer' => '',
			]),
		]));

	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropForeign(['plan_id']);
			$table->dropColumn('paid_until');
			$table->dropColumn('stripe_id');
			$table->dropColumn('iban_account');
			$table->dropColumn('payment_method');
			$table->dropColumn('payment_interval');
			$table->dropColumn('plan_id');
		});
		Schema::drop('plans');
	}
}
