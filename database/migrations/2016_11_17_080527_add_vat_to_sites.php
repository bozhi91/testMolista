<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVatToSites extends Migration
{

	public function up() {
		// Add vat to site
		Schema::table('sites', function (Blueprint $table) {
			$table->float('vat')->default(21)->after('country_id');
		});

		// Add vat to sites_payments
		Schema::table('sites_payments', function (Blueprint $table) {
			$table->float('payment_vat')->default(21)->after('payment_rate');
		});

		// Recalculate commissions
		\App\Models\Site\Payment::whereNotNull('reseller_id')->chunk(10, function ($payments) {
			foreach ($payments as $payment) 
			{
				$payment_net_amount = $payment->payment_amount / ( ( 100 + $payment->payment_vat ) / 100 );
				$reseller_amount = $payment->reseller_fixed + ( $payment_net_amount * $payment->reseller_variable / 100 );
				$payment->update([
					'reseller_amount' => $reseller_amount,
				]);
			}
		});
	}

	public function down() {
		Schema::table('sites_payments', function (Blueprint $table) {
			$table->dropColumn('payment_vat');
		});
		Schema::table('sites', function (Blueprint $table) {
			$table->dropColumn('vat');
		});
	}

}
