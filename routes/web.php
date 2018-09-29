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

Route::get('/', 'DatabaseController@query1');

Route::get('/query1', 'DatabaseController@query1');
Route::get('/query2', 'DatabaseController@query2');
Route::get('/query3', 'DatabaseController@query3');
Route::get('/query4', 'DatabaseController@query4');
Route::get('/query5', 'DatabaseController@query5');
Route::get('/query6', 'DatabaseController@query6');
Route::get('/query7', 'DatabaseController@query7');
Route::get('/query8', 'DatabaseController@query8');

