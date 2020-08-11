<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePostedAtToDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->renameColumn('posted_at', 'date');
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
            $table->renameColumn('date', 'posted_at');
        });
    }
}
