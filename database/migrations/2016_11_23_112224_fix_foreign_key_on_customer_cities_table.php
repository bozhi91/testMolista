<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixForeignKeyOnCustomerCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers_cities', function (Blueprint $table) {
            $table->dropForeign('customers_cities_city_id_foreign');

            $table->foreign('city_id')
					->references('id')
					->on('cities')
					->onUpdate('cascade')
					->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers_cities', function (Blueprint $table) {
            $table->dropForeign('customers_cities_city_id_foreign');

            $table->foreign('city_id')
					->references('id')
					->on('districts')
					->onUpdate('cascade')
					->onDelete('cascade');
        });
    }
}
