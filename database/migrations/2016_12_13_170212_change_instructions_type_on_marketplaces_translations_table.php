<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeInstructionsTypeOnMarketplacesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketplaces_translations', function (Blueprint $table) {
            $table->text('instructions')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketplaces_translations', function (Blueprint $table) {
            $table->string('instructions')->change();
        });
    }
}
