<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
//Route::group(['middleware' => 'csp'], function () {
//    Route::get('api/documentation/list/sdfhj3rhjegugwse87g93485gkjkjsbfkjbddvugbudgfu3jbkrbfjfsfd', '\L5Swagger\Http\Controllers\SwaggerController@api')
//        ->name('l5-swagger.default.api');
//});