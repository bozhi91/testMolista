<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanTables extends Migration
{
	public function up()
	{
		// Plans
		Schema::create('plans', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->string('code')->unique()->index();
			$table->string('name');
			$table->boolean('is_free');
			$table->float('price_year')->nullable();
			$table->float('price_month')->nullable();
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
		// Subscriptions
		Schema::create('subscriptions', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->nullable();
			$table->string('name');
			$table->string('stripe_id');
			$table->string('stripe_plan');
			$table->integer('quantity');
			$table->timestamp('trial_ends_at')->nullable();
			$table->timestamp('ends_at')->nullable();
			$table->timestamps();
		});
		// Site columns
		Schema::create('sites_planchanges', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->nullable();
			$table->bigInteger('plan_id')->unsigned()->nullable();
			$table->string('payment_interval');
			$table->string('payment_method');
			$table->text('old_data');
			$table->text('new_data');
			$table->text('invoicing');
			$table->text('response');
			$table->string('status')->default('pending');
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('set null');
		});
		// Site columns
		Schema::table('sites', function(Blueprint $table)
		{
			$table->bigInteger('plan_id')->unsigned()->nullable();
			$table->string('payment_interval')->nullable();
			$table->string('payment_method')->nullable();
			$table->string('iban_account')->nullable();
			$table->string('stripe_id')->nullable()->index();
			$table->string('card_brand')->nullable();
			$table->string('card_last_four')->nullable();
			$table->timestamp('trial_ends_at')->nullable();
			$table->dateTime('paid_until')->nullable();
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('set null');
		});
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropForeign(['plan_id']);
			$table->dropColumn('paid_until');
			$table->dropColumn('trial_ends_at');
			$table->dropColumn('card_last_four');
			$table->dropColumn('card_brand');
			$table->dropColumn('stripe_id');
			$table->dropColumn('iban_account');
			$table->dropColumn('payment_method');
			$table->dropColumn('payment_interval');
			$table->dropColumn('plan_id');
		});
		Schema::drop('sites_planchanges');
		Schema::drop('subscriptions');
		Schema::drop('plans');
	}
}
