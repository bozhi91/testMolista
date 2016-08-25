<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOwnershipToProperties extends Migration
{
    public function up()
    {
        Schema::table('properties', function(Blueprint $table)
        {
            $table->boolean('bank_owned')->after('construction_year');
            $table->boolean('private_owned')->after('construction_year');
        });
    }

    public function down()
    {
        Schema::table('properties', function(Blueprint $table)
        {
            $table->dropColumn('private_owned');
            $table->dropColumn('bank_owned');
        });
    }
}
