<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActionFieldToApiPiblicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_publications', function (Blueprint $table) {
            $table->string('action')->default('publish')->after('property');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_publications', function (Blueprint $table) {
            $table->dropColumn('action');
        });
    }
}
