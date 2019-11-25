<?php

use Illuminate\Http\Request;

// Custom: Delete as soon as possible
use App\Http\Controllers\JwtController;
use Illuminate\Support\Facades\Validator;



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
Route::get('/devices/list', function(){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Device::List($sub);

});



###
Route::get('/devices/list/free', function(){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Device::Free($sub) ;

});



###
Route::post('/devices/{device}', function( $device ){
    
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $created = App\Device::Create($sub, $device);

    if ( $created === false )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    if ( is_null( $created ) )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: resource already exists'
        ], 409 )->send();

    return response( '', 204 )->send();

})->where(['device' => '[a-z0-9]+']);



###
Route::delete('/devices/{device}', function( $device ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $deleted = App\Device::Remove($sub, $device);

    if ( $deleted === false )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    return response( '', 204 )->send();

})->where(['device' => '[a-z0-9]+']);



## Update a device and set new values for it
Route::put('/devices/profile/{device}', function( Request $request, $device){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    # Check input fields
    $validator = Validator::make($request->all(), [
        'type'        => 'string|nullable|max:50',
        'description' => 'string|nullable|max:50',
    ]);

    if ( $validator->fails() ){
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();
    }

    # Save only present fields
    $newValues = [];
    if ( $request->has('type') )
        $newValues['type'] = $request->input('type');

    if ( $request->has('description') )
        $newValues['description'] = $request->input('description');

    # Try to save coordinates and check errors
    $changeField = App\Device::Change( $sub, $device, $newValues);
    
    if ( is_null($changeField) ){
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: unable to save some field'
        ], 400 )->send();
    }

    return response( '', 204 )->send();

})->where(['device' => '[a-z0-9]+']);


###
Route::get('/devices/messages/{device}/{number?}', function($device, $number = 3){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Device::GetMessages( $sub, $device, $number );

})->where(['device' => '[a-z0-9]+', 'number' => '[0-9]+']);


###
Route::post('/devices/message/{device}', function( Request $request, $device ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    # Check if there is a message into the JSON
    if ( !$request->filled('message') ) {
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: message is missing'
        ], 400 )->send();
    }

    $newMessage =  App\Device::SetMessage( $sub, $device, $request->input('message') );

    if ( $newMessage === false )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();

    return response( '', 204 )->send();

})->where(['device' => '[a-z0-9]+']);


###
Route::post('/devices/relation/{device}/{group}', function( Request $request, $device, $group ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $newLink = App\Relation::Create( $sub, $device, $group);

    if ( $newLink === false )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();
    
    if ( is_null( $newLink ) )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: resource already exists'
        ], 400 )->send();

    return response( '', 204 )->send();

})->where(['device' => '[a-z0-9]+', 'group' => '[a-z0-9]+']);


###
Route::delete('/devices/relation/{device}', function( Request $request, $device ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $removeLink = App\Relation::Remove( $sub, $device );

    if ( $removeLink === false )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();
    
    if ( is_null( $removeLink ) )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: resource was not removed. It is possible it did not exists'
        ], 400 )->send();

    return response( '', 204 )->send();

})->where(['device' => '[a-z0-9]+']);




## Update a relation and set the coordinates for it
Route::put('/devices/relation/coordinates/{device}', function( Request $request, $device){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    # Check if there are coordinates into the JSON
    $validator = Validator::make($request->all(), [
        'map_x' => 'required|numeric|max:255',
        'map_y' => 'required|numeric|max:255',
    ]);

    if ($validator->fails()) 
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();

    # Try to save coordinates and check errors
    $changeCoords = App\Relation::Change( $sub, $device, [
        'map_x' => $request->input('map_x'),
        'map_y' => $request->input('map_y')
    ]);

    if ( $changeCoords === false )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field or not found'
        ], 400 )->send();
    
    if ( is_null($changeCoords) )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: unable to save some coordinate'
        ], 400 )->send();

    return response( '', 204 )->send();

})->where(['device' => '[a-z0-9]+']);



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
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    if ( is_null( $created ) )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: resource already exists'
        ], 409 )->send();

    return response( '', 204 )->send();

})->where(['group' => '[a-z0-9]+']);



###
Route::delete('/groups/{group}', function( $group ){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    $deleted = App\Group::Remove($sub, $group);

    if ( $deleted === false )
        return response()->json([
            'status'    => 'error',
            'message'   => 'Bad request: malformed field'
        ], 400 )->send();

    return response( '', 204 )->send();

})->where(['group' => '[a-z0-9]+']);

