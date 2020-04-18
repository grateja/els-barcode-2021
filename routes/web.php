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
            Route::get('{code}/add-to-queue/{origin?}', 'Web\QueuesController@show')->name('scan.queues');

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
            // /web/finished-goods/{model?}/add-profile
            Route::get('{model?}/add-profile', 'Web\FinishedGoodsController@addProfile');

            // /web/finished-goods/insert
            Route::post('insert', 'Web\FinishedGoodsController@insert');

            // /web/finished-goods/{model}/update
            Route::post('{model}/update', 'Web\FinishedGoodsController@update');

            // /web/finished-goods/items
            Route::group(['prefix' => 'items'], function () {
                // /web/finished-goods/items/{serialNumber}/add-to-inventory
                Route::get('{serialNumber}/add-to-inventory', 'Web\FinishedGoodItemsController@addToInventory');

                // /web/finished-goods/items/insert
                Route::post('insert', 'Web\FinishedGoodItemsController@insert');

                // /web/finished-goods/items/{serialNumber}/update
                Route::post('{serialNumber}/update', 'Web\FinishedGoodItemsController@update');

                // /web/finished-goods/items/{serialNumber}/delete
                Route::get('{serialNumber}/delete', 'Web\FinishedGoodItemsController@delete');

                // /web/finished-goods/items/{serialNumber}/delete-continue/{force?}
                Route::post('{serialNumber}/delete-continue/{force?}', 'Web\FinishedGoodItemsController@deleteContinue');

                // /web/finished-goods/items/{serialNumber}/restore
                Route::get('{serialNumber}/restore', 'Web\FinishedGoodItemsController@restore');

                // /web/finished-goods/items/{serialNumber}/restore-continue
                Route::post('{serialNumber}/restore-continue', 'Web\FinishedGoodItemsController@restoreContinue');

                // /web/finished-goods/items/{serialNumber}/add-remarks
                Route::get('{serialNumber}/add-remarks', 'Web\FinishedGoodItemsController@addRemarks');

                // /web/finished-goods/items/{id}/add-remarks-continue
                Route::post('{id}/add-remarks-continue', 'Web\FinishedGoodItemsController@addRemarksContinue');

                // /web/finished-goods/items/{serialNumber}/move
                Route::get('{serialNumber}/move', 'Web\FinishedGoodItemsController@move');

                // /web/finished-goods/items/{id}/move-continue
                Route::post('{id}/move-continue', 'Web\FinishedGoodItemsController@moveContinue');
            });
        });

        // /web/spare-parts
        Route::group(['prefix' => 'spare-parts'], function () {
            // /web/spare-parts/{model?}/add-profile
            Route::get('{model?}/add-profile', 'Web\SparePartsController@addProfile');

            // /web/spare-parts/insert
            Route::post('insert', 'Web\SparePartsController@insert');

            // /web/spare-parts/{model}/update
            Route::post('{model}/update', 'Web\SparePartsController@update');

            // /web/spare-parts/items
            Route::group(['prefix' => 'items'], function () {
                // /web/spare-parts/items/{serialNumber}/add-to-inventory
                Route::get('{serialNumber}/add-to-inventory', 'Web\SparePartItemsController@addToInventory');

                // /web/spare-parts/items/insert
                Route::post('insert', 'Web\SparePartItemsController@insert');

                // /web/spare-parts/items/{serialNumber}/update
                Route::post('{serialNumber}/update', 'Web\SparePartItemsController@update');

                // /web/spare-parts/items/{serialNumber}/delete
                Route::get('{serialNumber}/delete', 'Web\SparePartItemsController@delete');

                // /web/spare-parts/items/{serialNumber}/delete-continue/{force?}
                Route::post('{serialNumber}/delete-continue/{force?}', 'Web\SparePartItemsController@deleteContinue');

                // /web/spare-parts/items/{serialNumber}/restore
                Route::get('{serialNumber}/restore', 'Web\SparePartItemsController@restore');

                // /web/spare-parts/items/{serialNumber}/restore-continue
                Route::post('{serialNumber}/restore-continue', 'Web\SparePartItemsController@restoreContinue');

                // /web/spare-parts/items/{serialNumber}/add-remarks
                Route::get('{serialNumber}/add-remarks', 'Web\SparePartItemsController@addRemarks');

                // /web/spare-parts/items/{id}/add-remarks-continue
                Route::post('{id}/add-remarks-continue', 'Web\SparePartItemsController@addRemarksContinue');

                // /web/spare-parts/items/{serialNumber}/move
                Route::get('{serialNumber}/move', 'Web\SparePartItemsController@move');

                // /web/spare-parts/items/{id}/move-continue
                Route::post('{id}/move-continue', 'Web\SparePartItemsController@moveContinue');
            });
        });

        // /web/fixed-asstes
        Route::group(['prefix' => 'fixed-assets'], function () {
            // /web/fixed-asstes/items
            Route::group(['prefix' => 'items'], function () {
                // /web/fixed-assets/items/{serialNumber}/add-to-inventory
                Route::get('{serialNumber}/add-to-inventory', 'Web\FixedAssetsController@addToInventory');

                // /web/fixed-assets/items/insert
                Route::post('insert', 'Web\FixedAssetsController@insert');

                // /web/fixed-assets/items/{serialNumber}/update
                Route::post('{serialNumber}/update', 'Web\FixedAssetsController@update');

                // /web/fixed-assets/items/{serialNumber}/delete
                Route::get('{serialNumber}/delete', 'Web\FixedAssetsController@delete');

                // /web/fixed-assets/items/{serialNumber}/delete-continue/{force}
                Route::post('{serialNumber}/delete-continue/{force}', 'Web\FixedAssetsController@deleteContinue');

                // /web/fixed-assets/items/{serialNumber}/add-remarks
                Route::get('{serialNumber}/add-remarks', 'Web\FixedAssetsController@addRemarks');

                // /web/fixed-assets/items/{serialNumber}/add-remarks-continue
                Route::post('{serialNumber}/add-remarks-continue', 'Web\FixedAssetsController@addRemarksContinue');
            });
        });
    });

    // /web/login
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('web.login');
});

//Auth::routes();


// /scan
Route::group(['prefix' => 'scan', 'middleware' => 'auth'], function() {
    // /scan/{code}
    Route::get('{code}', 'Web\SerialProfilerController@show')->name('scan.any');

    // /scan/finished-goods/{serialNumber}
    Route::get('finished-goods/{serialNumber}', 'Web\FinishedGoodItemsController@show')->name('scan.finished-goods');

    // /scan/finished-goods/{model}/profile
    Route::get('finished-goods/{model}/profile', 'Web\FinishedGoodsController@show')->name('scan.finished-goods-profile');

    // /scan/spare-parts/{serialNumber}
    Route::get('spare-parts/{serialNumber}', 'Web\SparePartItemsController@show')->name('scan.spare-parts');

    // /scan/spare-parts/{model}/profile
    Route::get('spare-parts/{model}/profile', 'Web\SparePartsController@show')->name('scan.spare-parts-profile');

    // /scan/fixed-asstes/{serialNumber}
    Route::get('fixed-asstes/{serialNumber}', 'Web\FixedAssetsController@show')->name('scan.fixed-assets');
});


Route::get('{any}', function() {
    return view('welcome');
})->where('any', '.*');
