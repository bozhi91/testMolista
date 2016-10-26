<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreasePriceBeforeRangeOnPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            \DB::statement('ALTER TABLE properties CHANGE COLUMN `price_before` `price_before` DOUBLE(15,2)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            \DB::statement('ALTER TABLE properties CHANGE COLUMN `price_before` `price_before` DOUBLE(8,2)');
        });
    }
}
