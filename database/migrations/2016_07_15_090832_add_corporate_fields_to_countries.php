<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCorporateFieldsToCountries extends Migration
{
	public function up()
	{
		Schema::table('countries', function(Blueprint $table)
		{
			$table->string('marketplaces_images')->after('currency');
			$table->string('feature_image')->after('currency');
			$table->text('pay_methods')->after('currency');
			$table->char('locale',2)->nullable()->after('currency')->default('es');
			$table->foreign('locale')->references('locale')->on('locales')->onUpdate('cascade')->onDelete('set null');
		});
		// Add stripe to all countries
		\DB::statement("UPDATE `countries` SET `pay_methods`='" . '["stripe"]' ."' WHERE 1");
		// Add stripe & transfer to Spain
		\DB::statement("UPDATE `countries` SET `pay_methods`='" . '["stripe","transfer"]' ."' WHERE `code`='ES'");
	}

	public function down()
	{
		Schema::table('countries', function(Blueprint $table)
		{
			$table->dropForeign(['locale']);
			$table->dropColumn('locale');
			$table->dropColumn('pay_methods');
			$table->dropColumn('feature_image');
			$table->dropColumn('marketplaces_images');
		});
	}
}
