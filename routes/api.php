<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Where API routes are defined. These routes are loaded by the RouteServiceProvider 
| within the "api" middleware group.
|
| Some global tags like {device}, {group}, {number} are defined in 
| RouteServiceProvider to make this file shorter
|
*/



/*
 *
 * Routes that must be hidden
 * because of they are done with
 * internal proposes
 * 
 */
Route::prefix('internal')->group(function () {
    /*
    |
    | GET /nodes
    | Get all the nodes for a user_id
    |
    */
    Route::get('/nodes/{userId}', 'V2\NodeController@ShowAll');



    /*
    |
    | POST /node
    | Create a node for a user_id
    |
    */
    Route::post('/node', 'V2\NodeController@CreateOne');



    /*
    |
    | PUT /node
    | Update a node for a user_id
    |
    */
    Route::put('/node', 'V2\NodeController@ChangeOne');



    /*
    |
    | DELETE /node
    | Delete a node for a user_id
    |
    */
    Route::delete('/node/{nodeId}/{userId}', 'V2\NodeController@RemoveOne');

});



/*
 *
 * Routes that need a JWT
 * 
 */
Route::middleware(['custom.jwt'])->group(function () {
    /*
    |
    | POST /device
    | Create new device
    |
    */
    Route::post('/device', 'V2\DeviceController@CreateOne')
        ->middleware('custom.scope:api_w');



    /*
    |
    | PUT /device
    | Update a device and set new values for it
    |
    */
    Route::put('/device', 'V2\DeviceController@ChangeOne')
        ->middleware('custom.scope:api_w');



    /*
    |
    | DELETE /device/{device}
    | Delete a device
    |
    */
    Route::delete('/device/{device}', 'V2\DeviceController@RemoveOne')
    ->middleware('custom.scope:api_d');



    /*
    |
    | GET /device/{device}
    | Get information of a device
    |
    */
    Route::get('/device/{device}', 'V2\DeviceController@ShowOne')
    ->middleware('custom.scope:api_r');



    /*
    |
    | GET /devices/list
    | Get a list of devices in the system
    |
    */
    Route::get('/devices/list/all', 'V2\DeviceController@ShowAll')
        ->middleware('custom.scope:api_r');



    /*
    |
    | GET /devices/list/free
    | Get a list of available devices in the system
    |
    */
    Route::get('/devices/list/free', 'V2\DeviceController@ShowFree')
        ->middleware('custom.scope:api_r');



    /*
    |
    | POST /device/message
    | Post a new message in a device's conversation
    |
    */
    Route::post('/device/message', 'V2\DeviceController@CreateMessage')
        ->middleware('custom.scope:api_w');



    /*
    |
    | GET /devices/messages/{device}/{number?}
    | Get N messages from given device
    |
    */
    Route::get('/device/messages/{device}/{number?}', 'V2\DeviceController@ShowMessages')
        ->middleware('custom.scope:api_r');



    /*
    |
    | POST /relation
    | Creates a new relation between selected device and selected group
    |
    */
    Route::post('/relation', 'V2\RelationController@CreateOne')
        ->middleware('custom.scope:api_w');



    /*
    |
    | DELETE /relation/{device}
    | Destroy all relations between selected device and any group
    |
    */
    Route::delete('/relation/{device}', 'V2\RelationController@RemoveOne')
        ->middleware('custom.scope:api_d');

        

    /*
    |
    | GET /groups/list/names
    | Get a list with all the groups in the system
    |
    */
    Route::get('/groups/list/names', 'V2\GroupController@ShowNames')
        ->middleware('custom.scope:api_r');



    /*
    |
    | GET /groups/list/all
    | Get a list of all groups with / without devices related
    |
    */
    Route::get('/groups/list/all', 'V2\GroupController@ShowAll')
        ->middleware('custom.scope:api_r');



    /*
    |
    | GET /group/{group}
    | Get all the info related to a group and its related devices
    |
    */
    Route::get('/group/{group}', 'V2\GroupController@ShowOne')
        ->middleware('custom.scope:api_r');



    /*
    |
    | GET /groups/messages/{group}/{number?}
    | Get N messages from the full conversation of the selected group
    |
    */
    Route::get('/group/messages/{group}/{number?}', 'V2\GroupController@ShowMessages')
        ->middleware('custom.scope:api_r');



    /*
    |
    | POST /group
    | Creates a new empty group in the system
    |
    */
    Route::post('/group', 'V2\GroupController@CreateOne')
        ->middleware('custom.scope:api_r');



    /*
    |
    | DELETE /groups/{group}
    | Deletes a group from the system
    |
    */
    Route::delete('/groups/{group}', 'V2\GroupController@RemoveOne')
        ->middleware('custom.scope:api_d');

});