<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlinksTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flinks_items', function (Blueprint $table) {
            $table->id();
            $table->string('login_id')->unique();
            $table->string('username')->nullable();
            $table->timestamp('last_refresh')->nullable();
            $table->boolean('is_scheduled_refresh')->nullable();
            $table->string('type')->nullable();
            $table->string('institution');
            $table->string('error')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('flinks_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('transit_number')->nullable();
            $table->string('institution_number')->nullable();
            $table->string('account_number');
            $table->string('currency');
            $table->string('type')->nullable();
            $table->string('category');
            $table->string('title');
            $table->json('balances');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('flinks_items');
            $table->json('holder')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('flinks_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('account_id');
            $table->foreign('account_id')->references('external_id')->on('flinks_accounts');
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->decimal('debit', 15, 2)->nullable();
            $table->decimal('credit', 15, 2)->nullable();
            $table->decimal('balance', 15, 2)->nullable();
            $table->date('date');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flinks_items');
        Schema::dropIfExists('flinks_accounts');
        Schema::dropIfExists('flinks_transactions');
    }
}
