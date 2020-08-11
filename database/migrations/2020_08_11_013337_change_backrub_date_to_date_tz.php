<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBackrubDateToDateTz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->dropColumn('posted_at');
        });

        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->timestampTz('posted_at')->after('account_id');
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
            $table->dropColumn('posted_at');
        });

        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->dateTime('posted_at')->after('account_id');
        });
    }
}
