<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTicketFields extends Migration
{
	public function up()
	{
		// Site id
		Schema::table('sites', function(Blueprint $table)
		{
			$table->string('ticket_owner_token')->after('enabled')->comment('Belongs to site owner. If ownership changes, so should the token');
			$table->string('ticket_site_id')->after('enabled');
		});
		// User id + token
		Schema::table('users', function(Blueprint $table)
		{
			$table->string('ticket_user_token')->after('autologin_token');
			$table->string('ticket_user_id')->after('autologin_token');
		});
		// Property id
		Schema::table('properties', function(Blueprint $table)
		{
			$table->string('ticket_item_id')->after('published_at');
		});
		// Customer id
		Schema::table('customers', function(Blueprint $table)
		{
			$table->string('ticket_contact_id')->after('validated');
		});
	}
	public function down()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->dropColumn('ticket_contact_id');
		});
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropColumn('ticket_item_id');
		});
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('ticket_user_id');
			$table->dropColumn('ticket_user_token');
		});
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('ticket_site_id');
			$table->dropColumn('ticket_owner_token');
		});
	}
}
