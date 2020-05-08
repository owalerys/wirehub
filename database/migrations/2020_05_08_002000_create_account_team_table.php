<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_team', function (Blueprint $table) {
            $table->unsignedInteger('account_id')->index();
            $table->foreignId('account_id')->references('id')->on('accounts');
            $table->unsignedInteger('team_id')->index();
            $table->foreignId('team_id')->references('id')->on('teams');
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
        Schema::dropIfExists('account_team');
    }
}
