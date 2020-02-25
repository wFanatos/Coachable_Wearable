<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesUserorgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_orgs', function (Blueprint $table) {
            $table->bigInteger('org_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
			$table->timestamps();

            $table->foreign('org_id')->references('id')->on('organizations');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_orgs');
    }
}
