<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('entradas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('site_id');
            $table->string('title');
            $table->string('body');
            $table->dateTime('created_at')->nullable();

            $table->foreign('site_id')->references('id')->on('sites');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('entradas');
    }
}
