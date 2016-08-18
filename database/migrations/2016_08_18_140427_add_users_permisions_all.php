<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersPermisionsAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('sites_users', function(Blueprint $table)
		{
			$table->boolean('can_delete_all')->after('can_view_all')->default(0);
			$table->boolean('can_edit_all')->after('can_view_all')->default(0);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sites_users', function(Blueprint $table)
		{
            $table->dropColumn('can_delete_all');
            $table->dropColumn('can_edit_all');
		});
    }
}
