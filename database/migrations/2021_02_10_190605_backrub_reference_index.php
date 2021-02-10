<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BackrubReferenceIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backrub_transactions', function (Blueprint $table) {
            $table->index(['sender_reference_number', 'receiver_reference_number'], 'backrub_transactions_reference_index');
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
            $table->dropIndex('backrub_transactions_reference_index');
        });
    }
}
