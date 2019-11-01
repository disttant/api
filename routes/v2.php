<?php

use Illuminate\Http\Request;
//use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\JwtController;



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



# Let's validate each token
App::call('App\Http\Controllers\AuthorizationController@validationRequest'); 


###
Route::get('/test', function(){
    
    return;

});
