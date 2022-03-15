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

Route::any('/401', function () {
    return response()->json([
        'message' => 'Unauthorized'
    ], 401);
})->name('login');

Route::post('/users', 'App\Http\Controllers\UserController@store');
Route::get('/users/{id}', 'App\Http\Controllers\UserController@show');
Route::put('/users/{id}', 'App\Http\Controllers\UserController@update');
Route::delete('/users/{id}', 'App\Http\Controllers\UserController@delete');

Route::post('/tokens', 'App\Http\Controllers\TokenController@store');

Route::post('/games', 'App\Http\Controllers\GameController@store');
Route::get('/games', 'App\Http\Controllers\GameController@index');
Route::get('/games/{id}', 'App\Http\Controllers\GameController@show');
Route::put('/games/{id}', 'App\Http\Controllers\GameController@update');
Route::delete('/games/{id}', 'App\Http\Controllers\GameController@delete');

Route::post('/reviews', 'App\Http\Controllers\ReviewController@store');
Route::get('/reviews/{id}', 'App\Http\Controllers\ReviewController@show');
Route::get('/reviews', 'App\Http\Controllers\ReviewController@index');
Route::delete('/reviews/{id}', 'App\Http\Controllers\ReviewController@delete');

Route::fallback(function () {
    return response()->json([
        'message' => 'Not Found'
    ], 404);
});
