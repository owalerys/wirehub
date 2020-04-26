<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaidTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->json('status');
            $table->string('external_id')->unique();
            $table->string('access_token');
            $table->json('institution')->nullable();
            $table->string('webhook');
            $table->json('error')->nullable();
            $table->json('available_products');
            $table->json('billed_products');
            $table->timestamp('consent_expiration_time')->nullable();
            $table->timestamps();
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('item_id')->nullable()->index();
            $table->foreign('item_id')->references('external_id')->on('items');
            $table->json('balances');
            $table->json('numbers')->nullable();
            $table->string('name');
            $table->string('mask')->nullable();
            $table->string('official_name')->nullable();
            $table->string('type');
            $table->string('subtype');
            $table->string('verification_status')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('account_id')->index();
            $table->foreign('account_id')->references('external_id')->on('accounts');
            $table->json('category')->nullable();
            $table->string('category_id')->nullable();
            $table->string('transaction_type');
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->string('iso_currency_code')->nullable();
            $table->string('unofficial_currency_code')->nullable();
            $table->date('date');
            $table->date('authorized_date')->nullable();
            $table->json('location');
            $table->json('payment_meta');
            $table->string('payment_channel');
            $table->boolean('pending');
            $table->string('pending_transaction_id')->nullable();
            $table->string('account_owner')->nullable();
            $table->string('transaction_code')->nullable();
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
        Schema::dropIfExists('items');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('transactions');
    }
}
