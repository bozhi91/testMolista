<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransferModeToStats extends Migration
{
	public function up()
	{
		Schema::table('sites_stats', function(Blueprint $table)
		{
			$table->integer('transfer')->after('rent')->comment('Propiedades publicadas para transferir');
			$table->double('transfer_price',15,2)->after('rent_price')->comment('Precio promedio de transferencia propiedades');
			$table->float('transfer_sqm')->after('rent_sqm')->comment('Superficie promedio en transferencia');
			$table->integer('transfer_closed')->after('rent_closed')->comment('Propiedades transferidas');
			$table->integer('transfer_visits')->after('rent_visits')->comment('Visitas realizadas para transferencia');
			$table->integer('current_transfer')->after('current_rent')->comment('Propiedades publicadas para transferencia hasta esta fecha');
			$table->double('current_transfer_price',15,2)->after('current_rent_price')->comment('Precio promedio de transferencia propiedades hasta esta fecha');
			$table->float('current_transfer_sqm')->after('current_rent_sqm')->comment('Superficie promedio en transferencia');
		});
		Schema::table('users_stats', function(Blueprint $table)
		{
			$table->integer('transfer')->after('rent')->comment('Propiedades publicadas para transferir');
			$table->integer('transfer_visits')->after('rent_visits')->comment('Visitas realizadas para transferencia');
			$table->integer('transfer_closed')->after('rent_closed')->comment('Propiedades transferidas');
		});
	}

	public function down()
	{
		Schema::table('users_stats', function(Blueprint $table)
		{
			$table->dropColumn('transfer_visits');
			$table->dropColumn('transfer_closed');
			$table->dropColumn('transfer');
		});
		Schema::table('sites_stats', function(Blueprint $table)
		{
			$table->dropColumn('current_transfer_sqm');
			$table->dropColumn('current_transfer_price');
			$table->dropColumn('current_transfer');
			$table->dropColumn('transfer_visits');
			$table->dropColumn('transfer_closed');
			$table->dropColumn('transfer_sqm');
			$table->dropColumn('transfer_price');
			$table->dropColumn('transfer');
		});
	}
}
