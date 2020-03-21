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
    ->middleware('request.scopechecker:api_r');



/*
|
| GET /devices/list/free
| Get a list of available devices in the system
|
*/
Route::get('/devices/list/free', 'V1\DeviceController@ListFree')
    ->middleware('request.scopechecker:api_r');



/*
|
| POST /devices/{device}
| Creates a new device in the system
|
*/
Route::post('/devices/{device}', 'V1\DeviceController@Create')
    ->middleware('request.scopechecker:api_w');



/*
|
| DELETE /devices/{device}
| Deletes a device from the system
|
*/
Route::delete('/devices/{device}', 'V1\DeviceController@Remove')
    ->middleware('request.scopechecker:api_d');



/*
|
| PUT /devices/profile/{device}
| Update a device and set new values for it
|
*/
Route::put('/devices/profile/{device}', 'V1\DeviceController@ChangeProfile')
    ->middleware('request.scopechecker:api_w');



/*
|
| GET /devices/profile/{device}
| Get N messages from given device
|
*/
Route::get('/devices/profile/{device}/', 'V1\DeviceController@GetProfile')
    ->middleware('request.scopechecker:api_r');



/*
|
| GET /devices/messages/{device}/{number?}
| Get N messages from given device
|
*/
Route::get('/devices/messages/{device}/{number?}', 'V1\DeviceController@GetMessages')
    ->middleware('request.scopechecker:api_r');



/*
|
| POST /devices/message/{device}
| Post a new message in the selected device conversation
|
*/
Route::post('/devices/message/{device}', 'V1\DeviceController@PostMessage')
    ->middleware('request.scopechecker:api_w');



/*
|
| POST /devices/relation/{device}/{group}
| Creates a new relation between selected device and selected group
|
*/
Route::post('/devices/relation/{device}/{group}', 'V1\RelationController@Create')
    ->middleware('request.scopechecker:api_w');



/*
|
| DELETE /devices/relation/{device}
| Destroy all relations between selected device and any group
|
*/
Route::delete('/devices/relation/{device}', 'V1\RelationController@Remove')
    ->middleware('request.scopechecker:api_d');

    

/*
|
| GET /groups/list
| Get a list with all the groups in the system
|
*/
Route::get('/groups/list', 'V1\GroupController@List')
    ->middleware('request.scopechecker:api_r');



/*
|
| GET /groups/list/related
| Get a list of all groups with devices related inside
|
*/
Route::get('/groups/list/related', 'V1\GroupController@RelatedList')
    ->middleware('request.scopechecker:api_r');



/*
|
| GET /groups/list/full
| Get a list of all groups with / without devices related
|
*/
Route::get('/groups/list/full', 'V1\GroupController@FullList')
    ->middleware('request.scopechecker:api_r');



/*
|
| GET /group/list/related/{group}
| Get all the info related to a group and its related devices
|
*/
Route::get('/group/list/related/{group}', 'V1\GroupController@RelatedTo')
    ->middleware('request.scopechecker:api_r');



/*
|
| GET /groups/messages/{group}/{number?}
| Get N messages from the full conversation of the selected group
|
*/
Route::get('/groups/messages/{group}/{number?}', 'V1\GroupController@GetMessages')
    ->middleware('request.scopechecker:api_r');



/*
|
| POST /groups/{group}
| Creates a new empty group in the system
|
*/
Route::post('/groups/{group}', 'V1\GroupController@Create')
    ->middleware('request.scopechecker:api_r');



/*
|
| DELETE /groups/{group}
| Deletes a group from the system
|
*/
Route::delete('/groups/{group}', 'V1\GroupController@Remove')
    ->middleware('request.scopechecker:api_d');

