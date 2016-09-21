<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResellersTable extends Migration
{
	public function up()
	{
		Schema::create('resellers', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->string('ref')->unique();
			$table->string('type');
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->char('locale', 2)->nullable();
			$table->text('details');
			$table->boolean('enabled')->default(1);
			$table->rememberToken();
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('set null');
		});
		Schema::create('resellers_plans', function (Blueprint $table) 
		{
			$table->bigInteger('reseller_id')->unsigned();
			$table->bigInteger('plan_id')->unsigned();
			$table->float('commission_percentage');
			$table->float('commission_fixed');
			$table->primary([ 'reseller_id', 'plan_id' ]);
			$table->foreign('reseller_id')->references('id')->on('resellers')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('cascade');
		});
		Schema::table('sites', function(Blueprint $table)
		{
			$table->bigInteger('reseller_id')->unsigned()->nullable()->after('id');
			$table->foreign('reseller_id')->references('id')->on('resellers')->onUpdate('cascade')->onDelete('set null');
		});
	}

	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropForeign(['reseller_id']);
			$table->dropColumn('reseller_id');
		});
		Schema::drop('resellers_plans');
		Schema::drop('resellers');
	}
}
