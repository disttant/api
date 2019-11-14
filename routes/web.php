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



//
Route::any('/{any}', function () {
    return 'Welcome!';
})->where('any', '.*');;



//
/*Route::fallback(function() {
    return response()->json(['message' => 'Not Found.'], 404);
});*/


/*Route::any('/', function () {
    return 'hola bebe';
});*/