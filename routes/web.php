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

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/calvn/1', 'BotManController@tinker');
Route::get('/calvn/2', 'BotManController@postServiceFlow');
Route::get('/calvn/3', 'BotManController@feedbackFlow');
