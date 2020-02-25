<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('event_id')->unsigned();
            $table->string('duration');
            $table->string('date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('start_altitude');
            $table->string('end_altitude');
            $table->string('extra_data');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('runs');
    }
}
