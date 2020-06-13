<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePlaidTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('items', 'plaid_items');
        Schema::rename('accounts', 'plaid_accounts');
        Schema::rename('transactions', 'plaid_transactions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('plaid_items', 'items');
        Schema::rename('plaid_accounts', 'accounts');
        Schema::rename('plaid_transactions', 'transactions');
    }
}
