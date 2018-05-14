<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateStripeMigration extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('stripe_migrate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('site_id');
            $table->integer('user_id');
            $table->string('stripe_id_old');
            $table->string('stripe_id_new')->nullable();
            $table->dateTime('synchronized_at')->nullable();
        });
        //Add foreign keys to the table.
        Schema::table('stripe_migrate', function ($table) {
            $table->foreign('site_id')->references('id')->on('sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('stripe_migrate');
    }
}
