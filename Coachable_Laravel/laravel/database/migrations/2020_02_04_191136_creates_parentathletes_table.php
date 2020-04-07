<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesParentathletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_athletes', function (Blueprint $table) {
            $table->bigInteger('parent_id')->unsigned();
            $table->bigInteger('athlete_id')->unsigned();
			$table->timestamps();

            $table->foreign('parent_id')->references('id')->on('users');
            $table->foreign('athlete_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parentathletes');
    }
}
