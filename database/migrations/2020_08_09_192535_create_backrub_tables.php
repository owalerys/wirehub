<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackrubTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backrub_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_id');
            $table->string('email');
            $table->string('password');
            $table->string('name')->nullable();
            $table->string('error')->nullable();
            $table->timestamps();
        });

        Schema::create('backrub_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_id');
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->string('institution');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('backrub_items')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('backrub_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_id');
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('backrub_accounts')->cascadeOnDelete();
            $table->dateTime('posted_at');
            $table->decimal('amount', 15, 2);
            $table->string('currency');
            $table->string('receiver_reference_number');
            $table->string('sender_name');
            $table->string('sender_address');
            $table->string('sender_reference_number');
            $table->string('receiver_name');
            $table->string('receiver_bank_account_number');
            $table->boolean('confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('backrub_transactions');
        Schema::dropIfExists('backrub_accounts');
        Schema::dropIfExists('backrub_items');
    }
}
