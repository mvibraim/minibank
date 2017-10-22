<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'minibank'], function () {
  	Route::get('/getClients', 'ClientController@getClients');
  	Route::post('/addClient', 'ClientController@addClient');

  	Route::post('/createAccount', 'Commands@createAccount')->middleware('handler');
  	Route::post('/depositMoney', 'Commands@depositMoney')->middleware('handler');
  	Route::post('/withdrawMoney', 'Commands@withdrawMoney')->middleware('handler');
  	Route::post('/sendEmail', 'Commands@sendEmail');

  	Route::get('/getAccounts/{client_id}', 'Queries@getAccounts');
  	Route::get('/getEvents/{account_id}', 'Queries@getEvents');
  	Route::get('/replay/{account_id}/{event_limit}', 'Queries@replay');
});