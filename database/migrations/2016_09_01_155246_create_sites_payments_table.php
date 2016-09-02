<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesPaymentsTable extends Migration
{
	public function up()
	{
		Schema::create('sites_payments', function (Blueprint $table) 
		{
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->nullable();
			$table->bigInteger('plan_id')->unsigned()->nullable();
			$table->string('trigger')->comment('Modo de creación: webhook, admin, etc');
			$table->timestamp('paid_from');
			$table->timestamp('paid_until');
			$table->string('payment_method')->comment('Método de pago: stripe, transfer');
			$table->float('payment_amount')->comment('Monto pagado');
			$table->string('payment_currency',3)->nullable()->comment('Currency del pago');
			$table->double('payment_rate',10,3)->comment('Ratio de conversión de payment_currency a EUR');
			$table->bigInteger('reseller_id')->unsigned()->nullable();
			$table->float('reseller_variable')->comment('Comisión porcentual');
			$table->float('reseller_fixed')->comment('Comisión fija');
			$table->float('reseller_amount');
			$table->boolean('reseller_paid')->default(0);
			$table->timestamp('reseller_date')->nullable()->comment('Fecha de pago al reseller');
			$table->double('reseller_rate',10,3)->comment('Ratio de conversión de reseller_amount a EUR');
			$table->text('data')->comment('Datos adicionales');
			$table->bigInteger('created_by')->unsigned()->nullable();
			$table->timestamps();
			$table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('payment_currency')->references('code')->on('currencies')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('reseller_id')->references('id')->on('resellers')->onUpdate('cascade')->onDelete('set null');
			$table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
		});
	}

	public function down()
	{
		Schema::drop('sites_payments');
	}
}
