<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TeamableMorph extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teamables', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('team_id')->constrained();
            $table->morphs('teamable');

            $table->index(['teamable_id', 'teamable_type']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('teamables');
    }
}
