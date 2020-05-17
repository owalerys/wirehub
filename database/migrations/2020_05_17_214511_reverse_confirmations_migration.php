<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReverseConfirmationsMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('confirmations');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('confirmations', function (Blueprint $table) {
            $table->foreignId('transaction_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }
}
