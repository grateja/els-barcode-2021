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

    // /api/incoming-reports/spare-parts
    Route::group(['prefix' => 'spare-parts'], function () {
        // /api/incoming-reports/spare-parts
        Route::get('/', 'Api\IncomingSparePartsController@index');

        // /api/incoming-reports/spare-parts/{reportId}/delete
        Route::post('{reportId}/delete', 'Api\IncomingSparePartsController@delete');

        // /api/incoming-reports/spare-parts/create
        Route::post('create', 'Api\IncomingSparePartsController@create');

        // /api/incoming-reports/spare-parts/{reportId}/update
        Route::post('{reportId}/update', 'Api\IncomingSparePartsController@update');

        // /api/incoming-reports/spare-parts/{reportId}/view-items
        Route::get('{reportId}/view-items', 'Api\IncomingSparePartReportItemsController@index');

        // /api/incoming-reports/spare-parts/{reportId}/add-items
        Route::post('{reportId}/add-items', 'Api\IncomingSparePartReportItemsController@addItems');

        // /api/incoming-reports/spare-parts/{reportId}/remove-items
        Route::post('{reportId}/remove-items', 'Api\IncomingSparePartReportItemsController@removeItems');
    });
});

// /api/outgoing-reports
Route::group(['prefix' => 'outgoing-reports', 'middleware' => 'auth:api'], function () {
    // /api/outgoing-reports/finished-goods
    Route::group(['prefix' => 'finished-goods'], function () {
        // /api/outgoing-reports/finished-goods/{reportId}
        Route::get('{reportId}', 'Api\OutgoingFinishedGoodsController@show');

        // /api/outgoing-reports/finished-goods
        Route::get('/', 'Api\OutgoingFinishedGoodsController@index');

        // /api/outgoing-reports/finished-goods/{reportId}/delete
        Route::post('{reportId}/delete', 'Api\OutgoingFinishedGoodsController@delete');

        // /api/outgoing-reports/finished-goods/create
        Route::post('create', 'Api\OutgoingFinishedGoodsController@create');

        // /api/outgoing-reports/finished-goods/{reportId}/update
        Route::post('{reportId}/update', 'Api\OutgoingFinishedGoodsController@update');

        // /api/outgoing-reports/finished-goods/{reportId}/view-items
        Route::get('{reportId}/view-items', 'Api\OutgoingFinishedGoodReportItemsController@index');

        // /api/outgoing-reports/finished-goods/{reportId}/add-items
        Route::post('{reportId}/add-items', 'Api\OutgoingFinishedGoodReportItemsController@addItems');

        // /api/outgoing-reports/finished-goods/{reportId}/remove-items
        Route::post('{reportId}/remove-items', 'Api\OutgoingFinishedGoodReportItemsController@removeItems');
    });

    // /api/outgoing-reports/spare-parts
    Route::group(['prefix' => 'spare-parts'], function () {
        // /api/outgoing-reports/spare-parts
        Route::get('/', 'Api\OutgoingSparePartsController@index');

        // /api/outgoing-reports/spare-parts/{reportId}/delete
        Route::post('{reportId}/delete', 'Api\OutgoingSparePartsController@delete');

        // /api/outgoing-reports/spare-parts/create
        Route::post('create', 'Api\OutgoingSparePartsController@create');

        // /api/outgoing-reports/spare-parts/{reportId}/update
        Route::post('{reportId}/update', 'Api\OutgoingSparePartsController@update');

        // /api/outgoing-reports/spare-parts/{reportId}/view-items
        Route::get('{reportId}/view-items', 'Api\OutgoingSparePartReportItemsController@index');

        // /api/outgoing-reports/spare-parts/{reportId}/add-items
        Route::post('{reportId}/add-items', 'Api\OutgoingSparePartReportItemsController@addItems');

        // /api/outgoing-reports/spare-parts/{reportId}/remove-items
        Route::post('{reportId}/remove-items', 'Api\OutgoingSparePartReportItemsController@removeItems');
    });
});

// /api/reservations
Route::group(['prefix' => 'reservations', 'middleware' => 'auth:api'], function () {
    // /api/reservations
    Route::get('', 'Api\ReservationsController@index');

    // /api/reservations/{reportId}
    Route::get('{reportId}', 'Api\ReservationsController@show');

    // /api/reservations/create
    Route::post('create', 'Api\ReservationsController@create');

    // /api/reservations/{reportId}/update
    Route::post('{reportId}/update', 'Api\ReservationsController@update');

    // /api/reservations/finished-goods
    Route::group(['prefix' => 'finished-goods'], function () {
        // /api/reservations/finished-goods/{reservationId}/view-items
        Route::get('{reservationId}/view-items', 'Api\ReservedFinishedGoodItemsController@index');

        // /api/reservations/finished-goods/{reportId}/add-items
        Route::post('{reportId}/add-items', 'Api\ReservedFinishedGoodItemsController@addItems');

        // /api/reservations/finished-goods/{reportId}/remove-items
        Route::post('{reportId}/remove-items', 'Api\ReservedFinishedGoodItemsController@removeItems');
    });

    // /api/reservations/spare-parts
    Route::group(['prefix' => 'spare-parts'], function () {
        // /api/reservations/spare-parts/{reservationId}/view-items
        Route::get('{reservationId}/view-items', 'Api\ReservedSparePartItemsController@index');

        // /api/reservations/spare-parts/{reportId}/add-items
        Route::post('{reportId}/add-items', 'Api\ReservedSparePartItemsController@addItems');

        // /api/reservations/spare-parts/{reportId}/remove-items
        Route::post('{reportId}/remove-items', 'Api\ReservedSparePartItemsController@removeItems');
    });
});

// /api/finished-goods
Route::group(['prefix' => 'finished-goods', 'middleware' => 'auth:api'], function () {
    // /api/finished-goods/profiles
    Route::group(['prefix' => 'profiles'], function () {
        Route::get('/', 'Api\FinishedGoodsController@index');

        // /api/finished-goods/profiles/create
        Route::post('create', 'Api\FinishedGoodsController@create');

        // /api/finished-goods/profiles/{id}/update
        Route::post('{id}/update', 'Api\FinishedGoodsController@update');

        // /api/finished-goods/profiles/{id}/delete
        Route::post('{id}/delete', 'Api\FinishedGoodsController@delete');
    });

    // /api/finished-goods/items
    Route::group(['prefix' => 'items'], function () {
        // /api/finished-goods/items/{model}
        Route::get('{model?}', 'Api\FinishedGoodItemsController@index');

        // /api/finished-goods/items/create
        Route::post('create', 'Api\FinishedGoodItemsController@create');

        // /api/finished-goods/items/{serialNumber}/update
        Route::post('{serialNumber}/update', 'Api\FinishedGoodItemsController@update');

        // /api/finished-goods/items/{model}/insert-serial
        Route::post('{model}/insert-serial', 'Api\FinishedGoodItemsController@insertSerial');

        // /api/finished-goods/items/{serialNumber}/update-serial
        Route::post('{serialNumber}/update-serial', 'Api\FinishedGoodItemsController@updateSerial');

        // /api/finished-goods/items/{serialNumber}/delete
        Route::post('{serialNumber}/delete', 'Api\FinishedGoodItemsController@delete');
    });

});

// /api/spare-parts
Route::group(['prefix' => 'spare-parts', 'middleware' => 'auth:api'], function () {
    // /api/spare-parts/profiles
    Route::group(['prefix' => 'profiles'], function () {
        Route::get('/', 'Api\SparePartsController@index');

        // /api/spare-parts/profiles/create
        Route::post('create', 'Api\SparePartsController@create');

        // /api/spare-parts/profiles/{id}/update
        Route::post('{id}/update', 'Api\SparePartsController@update');

        // /api/spare-parts/profiles/{id}/delete
        Route::post('{id}/delete', 'Api\SparePartsController@delete');
    });

    // /api/spare-parts/items
    Route::group(['prefix' => 'items'], function () {
        // /api/spare-parts/items/{partNumber?}
        Route::get('{partNumber?}', 'Api\SparePartItemsController@index');

        // /api/spare-parts/items/create
        Route::post('create', 'Api\SparePartItemsController@create');

        // /api/finished-goods/items/{serialNumber}/update
        Route::post('{serialNumber}/update', 'Api\SparePartItemsController@update');

        // /api/finished-goods/items/{model}/insert-serial
        Route::post('{model}/insert-serial', 'Api\SparePartItemsController@insertSerial');

        // /api/finished-goods/items/{serialNumber}/update-serial
        Route::post('{serialNumber}/update-serial', 'Api\SparePartItemsController@updateSerial');

        // /api/finished-goods/items/{serialNumber}/delete
        Route::post('{serialNumber}/delete', 'Api\SparePartItemsController@delete');
    });
});

// /api/fixed-assets
Route::group(['prefix' => 'fixed-assets'], function() {
    // /api/fixed-assets/generate-serial
    Route::get('generate-serial', 'Api\FixedAssetsController@generateSerial');
});

// /api/clients
Route::group(['prefix' => 'clients', 'middleware' => 'auth:api'], function () {
    Route::get('/', 'Api\ClientsController@index');

    // /api/clients/create
    Route::post('create', 'Api\ClientsController@create');
});

// /api/subdealers
Route::group(['prefix' => 'subdealers', 'middleware' => 'auth:api'], function () {
    Route::get('/', 'Api\SubdealersController@index');

    // /api/subdealers/create
    Route::post('create', 'Api\SubdealersController@create');
});

// /api/printer
Route::group(['prefix' => 'printer'], function () {
    // /api/printer/serial-numbers
    Route::get('serial-numbers', 'PrinterController@serialNumbers');
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
