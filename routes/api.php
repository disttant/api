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
    Route::get('/nodes/{userId}', 'NodeController@ShowAll');



    /*
    |
    | POST /node
    | Create a node for a user_id
    |
    */
    Route::post('/node', 'NodeController@CreateOne');



    /*
    |
    | PUT /node
    | Update a node for a user_id
    |
    */
    Route::put('/node', 'NodeController@ChangeOne');



    /*
    |
    | DELETE /node
    | Delete a node for a user_id
    |
    */
    Route::delete('/node/{nodeId}/{userId}', 'NodeController@RemoveOne');

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
    Route::post('/device', 'DeviceController@CreateOne')
        ->middleware('custom.scope:user_card');



    /*
    |
    | PUT /device
    | Update a device and set new values for it
    |
    */
    Route::put('/device', 'DeviceController@ChangeOne')
        ->middleware('custom.scope:user_card');



    /*
    |
    | DELETE /device/{device}
    | Delete a device
    |
    */
    Route::delete('/device/{device}', 'DeviceController@RemoveOne')
    ->middleware('custom.scope:user_card');



    /*
    |
    | GET /device/{device}
    | Get information of a device
    |
    */
    Route::get('/device/{device}', 'DeviceController@ShowOne')
    ->middleware('custom.scope:user_card');



    /*
    |
    | GET /devices/list
    | Get a list of devices in the system
    |
    */
    Route::get('/devices/list/all', 'DeviceController@ShowAll')
        ->middleware('custom.scope:user_card');



    /*
    |
    | GET /devices/list/free
    | Get a list of available devices in the system
    |
    */
    Route::get('/devices/list/free', 'DeviceController@ShowFree')
        ->middleware('custom.scope:user_card');



    /*
    |
    | POST /device/message
    | Post a new message in a device's conversation
    |
    */
    Route::post('/device/message', 'DeviceController@CreateMessage')
        ->middleware('custom.scope:user_card');



    /*
    |
    | GET /devices/messages/{device}/{number?}
    | Get N messages from given device
    |
    */
    Route::get('/device/messages/{device}/{number?}', 'DeviceController@ShowMessages')
        ->middleware('custom.scope:user_card');



    /*
    |
    | POST /relation
    | Creates a new relation between selected device and selected group
    |
    */
    Route::post('/relation', 'RelationController@CreateOne')
        ->middleware('custom.scope:user_card');



    /*
    |
    | DELETE /relation/{device}
    | Destroy all relations between selected device and any group
    |
    */
    Route::delete('/relation/{device}', 'RelationController@RemoveOne')
        ->middleware('custom.scope:user_card');

        

    /*
    |
    | GET /groups/list/names
    | Get a list with all the groups in the system
    |
    */
    Route::get('/groups/list/names', 'GroupController@ShowNames')
        ->middleware('custom.scope:user_card');



    /*
    |
    | GET /groups/list/all
    | Get a list of all groups with / without devices related
    |
    */
    Route::get('/groups/list/all', 'GroupController@ShowAll')
        ->middleware('custom.scope:user_card');



    /*
    |
    | GET /group/{group}
    | Get all the info related to a group and its related devices
    |
    */
    Route::get('/group/{group}', 'GroupController@ShowOne')
        ->middleware('custom.scope:user_card');



    /*
    |
    | GET /groups/messages/{group}/{number?}
    | Get N messages from the full conversation of the selected group
    |
    */
    Route::get('/group/messages/{group}/{number?}', 'GroupController@ShowMessages')
        ->middleware('custom.scope:user_card');



    /*
    |
    | POST /group
    | Creates a new empty group in the system
    |
    */
    Route::post('/group', 'GroupController@CreateOne')
        ->middleware('custom.scope:user_card');



    /*
    |
    | DELETE /group/{group}
    | Deletes a group from the system
    |
    */
    Route::delete('/group/{group}', 'GroupController@RemoveOne')
        ->middleware('custom.scope:user_card');

});