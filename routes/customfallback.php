<?php

use Illuminate\Http\Request;



/*
|--------------------------------------------------------------------------
| Root Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('/{any}', function () {
    
    response()->json([
        'status'    => 'error',
        'message'   => 'Bad request: route not found'
    ], 404 )->send();

})->where('any', '.*');

