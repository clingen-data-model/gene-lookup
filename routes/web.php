<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'OmimLookupController@index');
    Route::post('/csv', 'OmimLookupController@getCsv');
    Route::post('/', 'OmimLookupController@query');
    Route::get('/test/info', 'TestController@phpinfo');
    Route::get('/test/route-helper', 'TestController@testRouteHelper');
// });

Route::redirect('/home', '/');

Auth::routes();
