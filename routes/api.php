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

    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return $sub;

});


###
Route::get('/channels/list/{perpage}/{page?}', function($perpage, $page = 1){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Channel::List($sub, $page, $perpage );

})->where(['perpage' => '[0-9]+', 'page' => '[0-9]+']);


###
Route::get('/channels/list/free/{perpage}/{page?}', function($perpage, $page = 1){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Channel::Free($sub, $page, $perpage ) ;

})->where(['perpage' => '[0-9]+', 'page' => '[0-9]+']);


###
Route::post('/channels/{channel}', function( $channel ){
    
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $created = App\Channel::Create($sub, $channel);

    if ( $created === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Malformed field'
        ], 400 )->send();

    if ( is_null( $created ) )
        response()->json([
            'status'    => 'error',
            'message'   => 'Resource already exists'
        ], 409 )->send();

    response( '', 204 )->send();

})->where(['channel' => '[a-z]+']);


###
Route::delete('/channels/{channel}', function( $channel ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $deleted = App\Channel::Remove($sub, $channel);

    if ( $deleted === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Malformed field'
        ], 400 )->send();

    response( '', 204 )->send();

})->where(['channel' => '[a-z]+']);


###
Route::get('/channels/messages/{channel}/{number?}', function($channel, $number = 3){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Channel::GetMessages( $sub, $channel, $number );

})->where(['channel' => '[a-z]+', 'number' => '[0-9]+']);


###
Route::post('/channels/message/{channel}', function( Request $request, $channel ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    # Check if there is a message into the JSON
    if ( !$request->filled('message') ) {
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: message is missing'
        ], 400 )->send();
    }

    $newMessage =  App\Channel::SetMessage( $sub, $channel, $request->input('message') );

    if ( $newMessage === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    response( '', 204 )->send();

})->where(['channel' => '[a-z]+']);







