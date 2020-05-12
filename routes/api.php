<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/item/exchange', 'PlaidController@exchangeToken');

    Route::get('/accounts', 'AccountController@getAccounts');
    Route::get('/accounts/{accountId}', 'AccountController@getAccount');
    Route::get('/accounts/{accountId}/transactions', 'AccountController@getAccountTransactions');

    Route::get('/teams', 'TeamController@getTeams');
    Route::get('/teams/{teamId}', 'TeamController@getTeam');
    Route::post('/teams', 'TeamController@createTeam');
});

Route::post('/webhook/plaid', 'WebhookController@plaid')->middleware('webhook.plaid');
