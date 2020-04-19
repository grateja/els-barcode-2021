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

    // /api/autocomplete/spare-parts
    Route::get('spare-parts', 'Api\SparePartsController@autocomplete');

    // /api/autocomplete/accounts
    Route::get('accounts', 'Api\AccountsController@autocomplete');
});

// /api/incoming-reports
Route::group(['prefix' => 'incoming-reports', 'middleware' => 'auth:api'], function () {
    // /api/incoming-reports/finished-goods
    Route::group(['prefix' => 'finished-goods'], function () {
        // /api/incoming-reports/finished-goods
        Route::get('/', 'Api\IncomingFinishedGoodsController@index');

        // /api/incoming-reports/finished-goods/{reportId}/delete
        Route::post('{reportId}/delete', 'Api\IncomingFinishedGoodsController@delete');

        // /api/incoming-reports/finished-goods/create
        Route::post('create', 'Api\IncomingFinishedGoodsController@create');

        // /api/incoming-reports/finished-goods/{reportId}/update
        Route::post('{reportId}/update', 'Api\IncomingFinishedGoodsController@update');

        // /api/incoming-reports/finished-goods/{reportId}/view-items
        Route::get('{reportId}/view-items', 'Api\IncomingFinishedGoodReportItemsController@index');

        // /api/incoming-reports/finished-goods/{reportId}/add-items
        Route::post('{reportId}/add-items', 'Api\IncomingFinishedGoodReportItemsController@addItems');

        // /api/incoming-reports/finished-goods/{reportId}/remove-items
        Route::post('{reportId}/remove-items', 'Api\IncomingFinishedGoodReportItemsController@removeItems');
    });
});

// /api/finished-goods
Route::group(['prefix' => 'finished-goods', 'middleware' => 'auth:api'], function () {
    // /api/finished-goods/items
    Route::group(['prefix' => 'items'], function () {
        // /api/finished-goods/items/
        Route::get('/', 'Api\FinishedGoodItemsController@index');

        // /api/finished-goods/items/create
        Route::post('create', 'Api\FinishedGoodItemsController@create');
    });

});

// /api/fixed-assets
Route::group(['prefix' => 'fixed-assets'], function() {
    // /api/fixed-assets/generate-serial
    Route::get('generate-serial', 'Api\FixedAssetsController@generateSerial');
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
