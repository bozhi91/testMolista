<?php

use Illuminate\Database\Migrations\Migration;

class CreateLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function ($table) {
            $table->bigIncrements('id');
            $table->bigIncrements('user_id')->nullable();
            $table->string('owner_type');
            $table->bigIncrements('owner_id');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logs');
    }
}
