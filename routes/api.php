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
    Route::post('/items/plaid', 'PlaidController@exchangeToken');
    Route::post('/items/flinks', 'FlinksController@saveLogin');

    Route::get('/items/{itemId}', 'ItemController@get');
    Route::delete('/items/{itemId}', 'ItemController@delete');

    Route::get('/accounts', 'AccountController@getAccounts');
    Route::get('/accounts/{accountId}', 'AccountController@getAccount');
    Route::patch('/accounts/{accountId}', 'AccountController@patchAccount');
    Route::get('/accounts/{accountId}/transactions', 'AccountController@getAccountTransactions');
    Route::get('/accounts/{accountId}/transactions/historical', 'AccountController@getAccountTransactionsHistorical');
    Route::put('/accounts/{accountId}/team', 'AccountController@putTeamLink');

    Route::get('/teams', 'TeamController@getTeams');
    Route::get('/teams/{teamId}', 'TeamController@getTeam');
    Route::post('/teams', 'TeamController@createTeam');
    Route::post('/teams/{teamId}/resend-invite', 'TeamController@resendInvite');

    Route::put('/transactions/{transactionId}/confirm', 'TransactionController@confirm');

    Route::get('/user', 'UserController@index');
    Route::patch('/user', 'UserController@update');
    Route::put('/user/password', 'UserController@updatePassword');
});

Route::post('/webhook/plaid', 'WebhookController@plaid')->middleware('webhook.plaid');
