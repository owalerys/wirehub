<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBackrubDateToDateTimeTz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->dropColumn('date');
        });

        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->dateTimeTz('date')->after('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->dropColumn('date');
        });

        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->dateTime('date')->after('account_id');
        });
    }
}
