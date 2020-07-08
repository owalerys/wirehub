<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmationsToFlinksTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flinks_transactions', function (Blueprint $table) {
            $table->boolean('confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable()->after('confirmed');
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
            $table->dropColumn('confirmed');
            $table->dropColumn('confirmed_at');
        });
    }
}
