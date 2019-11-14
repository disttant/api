<?php

use Illuminate\Http\Request;
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


###
Route::get('/channels/list', function(){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Channel::List($sub);

});


###
Route::get('/channels/list/free', function(){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Channel::Free($sub) ;

});


###
Route::post('/channels/{channel}', function( $channel ){
    
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $created = App\Channel::Create($sub, $channel);

    if ( $created === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    if ( is_null( $created ) )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: resource already exists'
        ], 409 )->send();

    response( '', 204 )->send();

})->where(['channel' => '[a-z0-9]+']);


###
Route::delete('/channels/{channel}', function( $channel ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $deleted = App\Channel::Remove($sub, $channel);

    if ( $deleted === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    response( '', 204 )->send();

})->where(['channel' => '[a-z0-9]+']);


###
Route::get('/channels/messages/{channel}/{number?}', function($channel, $number = 3){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Channel::GetMessages( $sub, $channel, $number );

})->where(['channel' => '[a-z0-9]+', 'number' => '[0-9]+']);


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
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();

    response( '', 204 )->send();

})->where(['channel' => '[a-z0-9]+']);


###
Route::post('/channels/link/{channel}/{group}', function( Request $request, $channel, $group ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $newLink = App\Channel::SetLink( $sub, $channel, $group);

    if ( $newLink === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();
    
    if ( is_null( $newLink ) )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: resource already exists'
        ], 400 )->send();

    response( '', 204 )->send();

})->where(['channel' => '[a-z0-9]+', 'group' => '[a-z0-9]+']);


###
Route::delete('/channels/link/{channel}', function( Request $request, $channel ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $removeLink = App\Channel::RemoveLink( $sub, $channel );

    if ( $removeLink === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();
    
    if ( is_null( $removeLink ) )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: resource was not removed. It is possible it did not exists'
        ], 400 )->send();

    response( '', 204 )->send();

})->where(['channel' => '[a-z0-9]+']);


###
Route::get('/groups/list', function(){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::List($sub);

});


###
Route::get('/groups/list/related', function(){

    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::RelatedList($sub);

});


###
Route::get('/groups/list/full', function(){

    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::FullList($sub);

});


###
Route::get('/groups/messages/{group}/{number?}', function($group, $number = 3){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::GetMessages( $sub, $group, $number );

})->where(['group' => '[a-z0-9]+', 'number' => '[0-9]+']);


###
Route::post('/groups/{group}', function( $group ){
    
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $created = App\Group::Create($sub, $group);

    if ( $created === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    if ( is_null( $created ) )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: resource already exists'
        ], 409 )->send();

    response( '', 204 )->send();

})->where(['group' => '[a-z0-9]+']);


###
Route::delete('/groups/{group}', function( $group ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $deleted = App\Group::Remove($sub, $group);

    if ( $deleted === false )
        response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    response( '', 204 )->send();

})->where(['group' => '[a-z0-9]+']);

