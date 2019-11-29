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



/*
|
| GET /devices/list
| Get a list of devices in the system
|
*/
Route::get('/devices/list', function(){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Device::List($sub);

});



/*
|
| GET /devices/list/free
| Get a list of available devices in the system
|
*/
Route::get('/devices/list/free', function(){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Device::Free($sub) ;

});



/*
|
| POST /devices/{device}
| Creates a new device in the system
|
*/
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



/*
|
| DELETE /devices/{device}
| Deletes a device from the system
|
*/
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



/*
|
| PUT /devices/profile/{device}
| Update a device and set new values for it
|
*/
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



/*
|
| GET /devices/messages/{device}/{number?}
| Get N messages from given device
|
*/
Route::get('/devices/messages/{device}/{number?}', function($device, $number = 3){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    return App\Device::GetMessages( $sub, $device, $number );

})->where(['device' => '[a-z0-9]+', 'number' => '[0-9]+']);



/*
|
| POST /devices/message/{device}
| Post a new message in the selected device conversation
|
*/
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



/*
|
| POST /devices/relation/{device}/{group}
| Creates a new relation between selected device and selected group
|
*/
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



/*
|
| DELETE /devices/relation/{device}
| Destroy all relations between selected device and any group
|
*/
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



/*
|
| PUT /devices/relation/coordinates/{device}
| Update a relation and set the coordinates for it
|
*/
Route::put('/devices/relation/coordinates/{device}', function( Request $request, $device){
    
    # FIND THE USER_ID INTO TOKEN
    $sub = App::call('App\Http\Controllers\JwtController@getSub');

    # Check if there are coordinates into the JSON
    $validator = Validator::make($request->all(), [
        'map_x' => 'required|integer|min:0|max:20',
        'map_y' => 'required|integer|min:0|max:20',
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

});



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

});



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

});



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

})->where(['group' => '[a-z0-9]+']);



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

})->where(['group' => '[a-z0-9]+', 'number' => '[0-9]+']);



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

})->where(['group' => '[a-z0-9]+']);



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

})->where(['group' => '[a-z0-9]+']);

