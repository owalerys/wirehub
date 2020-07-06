<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlinksDateAmountDescriptionIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flinks_transactions', function (Blueprint $table) {
            $table->index(['account_id', 'date', 'debit', 'credit', 'description'], 'flinks_reverse_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flinks_transactions', function (Blueprint $table) {
            $table->dropIndex('flinks_reverse_lookup_index');
        });
    }
}
