<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountNicknames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plaid_accounts', function (Blueprint $table) {
            $table->string('nickname')->nullable();
        });

        Schema::table('flinks_accounts', function (Blueprint $table) {
            $table->string('nickname')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plaid_accounts', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });

        Schema::table('flinks_accounts', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }
}
