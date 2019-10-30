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
//App::call('App\Http\Controllers\AuthorizationController@validationRequest'); 
//$sandbox = App::call('App\Http\Controllers\JwtController@getSandbox');


Route::get('/test', function(){

});




Route::get('/channels/list/{perpage}/{page?}', function($perpage, $page = 1){
    
    # FIND THE SANDBOX INTO TOKEN
    $sandbox = App::call('App\Http\Controllers\JwtController@getSandbox');

    return App\Channel::List($sandbox, $page, $perpage ) ;

})->where(['perpage' => '[0-9]+', 'page' => '[0-9]+']);



Route::get('/channels/list/free/{perpage}/{page?}', function($perpage, $page = 1){
    
    # FIND THE SANDBOX INTO TOKEN
    $sandbox = App::call('App\Http\Controllers\JwtController@getSandbox');

    return App\Channel::Free($sandbox, $page, $perpage ) ;

})->where(['perpage' => '[0-9]+', 'page' => '[0-9]+']);



Route::post('/channels/{channel}', function( $channel ){
    
    # FIND THE SANDBOX INTO TOKEN
    $sandbox = App::call('App\Http\Controllers\JwtController@getSandbox');

    return App\Channel::Create($sandbox, $channel);

})->where(['channel' => '[a-z]+']);











Route::get('/dame', function(){
    
    return App\Channel::Messages('507e1af4-fa5a-11e9-8f0b-362b9e155587', 'caca', 2) ;

});


Route::get('/new/message', function(){
    
    $channel = new App\Message;

    $channel->sandbox = '507e1af4-fa5a-11e9-8f0b-362b9e155587';
    $channel->channel_id = 1;
    $channel->message = 'hola soy un mensaje de prueba 2 en caca';

    $channel->save();

});