<?php

use App\Http\Controllers\Orders\OrderController;
use Illuminate\Support\Facades\Route;

Route::group([
//    'middleware' => ['api', 'auth:api'],
    'prefix' => 'orders'
], function () {
    // List and search
    Route::get('/', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/statuses', [OrderController::class, 'getStatusList'])
        ->name('orders.statuses');

    // Create
    Route::post('/', [OrderController::class, 'store'])
        ->name('orders.store');

    // Show, update, delete
    Route::get('{id}', [OrderController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('orders.show');

    Route::match(['put', 'patch'], '{id}', [OrderController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('orders.update');

    Route::delete('{id}', [OrderController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('orders.destroy');

    Route::get('getOrdersReport', [OrderController::class, 'getOrdersReport'])
        ->name('orders.report.get');

});