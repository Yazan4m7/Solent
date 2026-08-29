<?php

use App\Modules\Stock\Http\Controllers\StockDashboardController;
use App\Modules\Stock\Http\Controllers\StockItemController;
use App\Modules\Stock\Http\Controllers\StockLocationController;
use App\Modules\Stock\Http\Controllers\StockMovementController;
use App\Modules\Stock\Http\Controllers\StockPurchaseController;
use App\Modules\Stock\Http\Controllers\StockSupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('stock')->name('stock.')->group(function () {
    Route::get('/', 'StockDashboardController@index')->name('index');
    Route::get('/needs', 'StockDashboardController@needs')->name('needs');

    Route::resource('items', 'StockItemController')->except(['destroy']);

    Route::get('/purchases', 'StockPurchaseController@index')->name('purchases.index');
    Route::get('/purchases/create', 'StockPurchaseController@create')->name('purchases.create');
    Route::post('/purchases', 'StockPurchaseController@store')->name('purchases.store');

    Route::get('/movements', 'StockMovementController@index')->name('movements.index');
    Route::get('/adjustments/create', 'StockMovementController@createAdjustment')->name('adjustments.create');
    Route::post('/adjustments', 'StockMovementController@storeAdjustment')->name('adjustments.store');

    Route::get('/suppliers', 'StockSupplierController@index')->name('suppliers.index');
    Route::post('/suppliers', 'StockSupplierController@store')->name('suppliers.store');

    Route::get('/locations', 'StockLocationController@index')->name('locations.index');
    Route::post('/locations', 'StockLocationController@store')->name('locations.store');
});
