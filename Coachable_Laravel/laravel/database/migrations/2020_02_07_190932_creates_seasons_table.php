<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('org_id')->unsigned();
            $table->string('season_name');
            $table->string('season_description');
            $table->string('season_start');
            $table->string('season_end');


            $table->foreign('org_id')->references('id')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seasons');
    }
}
