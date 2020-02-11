<?php

use Illuminate\Http\Request;

// Custom: Delete as soon as possible
use App\Http\Controllers\JwtController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Where API routes are defined. These routes are loaded by the RouteServiceProvider 
| within the "api" middleware group.
|
| Some global tags like {device}, {group}, {number} are defined in 
| RouteServiceProvider to short this file a bit
|
*/



/*
|
| GET /devices/list
| Get a list of devices in the system
|
*/
Route::get('/devices/list', 'V1\DeviceController@List')
    ->middleware('request.scopechecker:adaptative_r');



/*
|
| GET /devices/list/free
| Get a list of available devices in the system
|
*/
Route::get('/devices/list/free', 'V1\DeviceController@ListFree')
    ->middleware('request.scopechecker:adaptative_r');



/*
|
| POST /devices/{device}
| Creates a new device in the system
|
*/
Route::post('/devices/{device}', 'V1\DeviceController@Create')
    ->middleware('request.scopechecker:adaptative_w');



/*
|
| DELETE /devices/{device}
| Deletes a device from the system
|
*/
Route::delete('/devices/{device}', 'V1\DeviceController@Remove')
    ->middleware('request.scopechecker:adaptative_d');



/*
|
| PUT /devices/profile/{device}
| Update a device and set new values for it
|
*/
Route::put('/devices/profile/{device}', 'V1\DeviceController@ChangeProfile')
    ->middleware('request.scopechecker:adaptative_w');



/*
|
| GET /devices/messages/{device}/{number?}
| Get N messages from given device
|
*/
Route::get('/devices/messages/{device}/{number?}', 'V1\DeviceController@GetMessages')
    ->middleware('request.scopechecker:adaptative_r');



/*
|
| POST /devices/message/{device}
| Post a new message in the selected device conversation
|
*/
Route::post('/devices/message/{device}', 'V1\DeviceController@PostMessage')
    ->middleware('request.scopechecker:adaptative_w');



/*
|
| POST /devices/relation/{device}/{group}
| Creates a new relation between selected device and selected group
|
*/
Route::post('/devices/relation/{device}/{group}', 'V1\RelationController@Create')
    ->middleware('request.scopechecker:adaptative_w');



/*
|
| DELETE /devices/relation/{device}
| Destroy all relations between selected device and any group
|
*/
Route::delete('/devices/relation/{device}', 'V1\RelationController@Remove')
    ->middleware('request.scopechecker:adaptative_d');



/*
|
| PUT /devices/relation/coordinates/{device}
| Update a relation and set the coordinates for it
|
*/
Route::put('/devices/relation/coordinates/{device}', 'V1\RelationController@Change')
    ->middleware('request.scopechecker:adaptative_w');



/*
|
| GET /groups/list
| Get a list with all the groups in the system
|
*/
Route::get('/groups/list', function(){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::List($sub);

})->middleware('request.scopechecker:adaptative_r');



/*
|
| GET /groups/list/related
| Get a list of all groups with devices related inside
|
*/
Route::get('/groups/list/related', function(){

    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::RelatedList($sub);

})->middleware('request.scopechecker:adaptative_r');



/*
|
| GET /groups/list/full
| Get a list of all groups with / without devices related
|
*/
Route::get('/groups/list/full', function(){

    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::FullList($sub);

})->middleware('request.scopechecker:adaptative_r');



/*
|
| GET /group/list/related/{group}
| Get all the info related to a group and its related devices
|
*/
Route::get('/group/list/related/{group}', function( $group ){

    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::RelatedTo( $sub, $group );

})->middleware('request.scopechecker:adaptative_r');



/*
|
| GET /groups/messages/{group}/{number?}
| Get N messages from the full conversation of the selected group
|
*/
Route::get('/groups/messages/{group}/{number?}', function($group, $number = 3){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Group::GetMessages( $sub, $group, $number );

})->middleware('request.scopechecker:adaptative_r');



/*
|
| POST /groups/{group}
| Creates a new empty group in the system
|
*/
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

})->middleware('request.scopechecker:adaptative_r');



/*
|
| DELETE /groups/{group}
| Deletes a group from the system
|
*/
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

})->middleware('request.scopechecker:adaptative_d');

