<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheckboxDesdeToPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //ALTER TABLE properties ADD desde boolean NULL;
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('desde')->nullable()->after('url_3d');
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
            $table->dropColumn('desde');
        });
    }
}
