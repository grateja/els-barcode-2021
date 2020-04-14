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

// /web
Route::group(['prefix' => 'web'], function() {
    Route::group(['middleware' => 'auth'], function () {
        // /web/default
        Route::get('default', 'Web\DefaultController@index');

        // /web/queues
        Route::group(['prefix' => 'queues'], function () {
            // /web/queues/{code}/add-to-queue/{origin?}
            Route::get('{code}/add-to-queue/{origin?}', 'Web\QueuesController@show');

            // /web/queues/save-to-queue
            Route::post('save-to-queue', 'Web\QueuesController@saveToQueue');

            // /web/queues/complete
            Route::post('complete', 'Web\QueuesController@complete');

            // /web/queues/clear
            Route::post('clear', 'Web\QueuesController@clear');

            // /web/queues
            Route::get('/', 'Web\QueuesController@index')->name('queues.default');

            // /web/queues/queue-items
            Route::group(['prefix' => 'queue-items'], function () {
                // /web/queues/queue-items/{id}/remove-item
                Route::post('{id}/remove-item', 'Web\QueueItemsController@deleteItem');
            });
        });

        // /web/finished-goods
        Route::group(['prefix' => 'finished-goods'], function () {
            // /web/finished-goods/{code}/add-remarks
            Route::get('{code}/add-remarks', 'Web\FinishedGoodsController@addRemarks');

            // /web/finished-goods/{id}/add-remarks-continue
            Route::post('{id}/add-remarks-continue', 'Web\FinishedGoodsController@addRemarksContinue');

            // /web/finished-goods/{code}/delete
            Route::get('{code}/delete', 'Web\FinishedGoodsController@deleteItem');

            // /web/finished-goods/{id}/delete-continue
            Route::post('{id}/delete-continue', 'Web\FinishedGoodsController@deleteItemContinue');

            // /web/finished-goods/{id}/delete-permanently
            Route::post('{id}/delete-permanently', 'Web\FinishedGoodsController@permanentlyDeleteItemContinue');

            // /web/finished-goods/{code}/move
            Route::get('{code}/move', 'Web\FinishedGoodsController@move');

            // /web/finished-goods/{id}/move-continue
            Route::post('{id}/move-continue', 'Web\FinishedGoodsController@moveContinue');

            // /web/finished-goods/{code}/add-to-inventory
            Route::get('{code}/add-to-inventory', 'Web\FinishedGoodsController@addToInventory');

            // /web/finished-goods/insert
            Route::post('insert', 'Web\FinishedGoodsController@insert');

            // /web/finished-goods/update/{id}
            Route::post('update/{id}', 'Web\FinishedGoodsController@update');
        });
    });

    // /web/login
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('web.login');
});

Auth::routes();


// /scan
Route::group(['prefix' => 'scan', 'middleware' => 'auth'], function() {
    // /scan/any/{code}
    Route::get('any/{code}', 'AnyController@show');

    // /scan/finished-goods/{code}
    Route::get('finished-goods/{code}', 'Web\FinishedGoodsController@show')->name('scan.finished-goods');
});


Route::get('{any}', function() {
    return view('welcome');
})->where('any', '.*');
