<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesUserteamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_teams', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('team_id')->unsigned();
			$table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userteams');
    }
}
