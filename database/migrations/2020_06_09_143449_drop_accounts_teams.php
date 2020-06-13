<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAccountsTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('account_team');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_team', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')->constrained();
            $table->foreignId('team_id')->constrained();
            $table->timestamps();

            $table->timestamp('deleted_at')->nullable();
        });
    }
}
