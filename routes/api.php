<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// /api/autocomplete
Route::group(['prefix' => 'autocomplete'], function () {
    // /api/autocomplete/finished-goods
    Route::get('finished-goods', 'Api\FinishedGoodsController@autocomplete');
});

// /api/users
Route::group(['prefix' => 'users', 'middleware' => 'auth:api'], function() {
    // /api/users/
    Route::get('/', 'UsersController@index');

    // /api/users/create
    Route::post('create', 'UsersController@create');

    // /api/users/{userId}/update
    Route::post('{userId}/update', 'UsersController@update');

    // /api/users/{userId}/change-password
    Route::post('{userId}/change-password', 'UsersController@changePassword');

    // /api/users/{userId}/delete-user
    Route::post('{userId}/delete-user', 'UsersController@deleteUser');

    // /api/users/{userId}/assign-role
    Route::post('{userId}/assign-role', 'UsersController@assignRole');
});

// oauth
Route::group(['prefix' => 'oauth', 'middleware' => 'auth:api'], function() {
    // oauth/check
    Route::get('check', 'OAuthController@check');

    // oauth/logout
    Route::post('logout', 'OAuthController@logout');
});

Route::group(['prefix' => 'oauth'], function() {
    Route::post('login', 'OAuthController@login');
});


Route::any('{any}', function() {
    return response()->json(['message' => 'Resource not found'], 404);
})->where('any', '.*');
