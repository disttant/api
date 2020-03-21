<?php

namespace App\Http\Controllers\V1;

use App\Device;
use App\Message;
use App\Relation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\JwtController as JwtController;



class DeviceController extends Controller
{



    /* *
     *
     *  List all available devices of the given user
     *
     * */
    public function List( Request $request )
    {
        $user_id = JwtController::getSub( $request );
        
        return Device::select('name', 'type', 'description')
            ->where('user_id', $user_id)
            ->get();
    }



    /* *
     *
     *  List all not related devices of the given user
     *
     * */
    public function ListFree( Request $request )
    {
        $user_id = JwtController::getSub( $request );

        return Device::select('name', 'type', 'description')
            ->where('user_id', $user_id)
            ->whereNotIn('id', 
                Relation::select('device_id')
                    ->whereColumn('device_id', 'devices.id')
                    ->where('user_id', $user_id)
            )
            ->get();
    }



    /* *
     *
     *  Creates a new device for the given user
     *
     * */
    public function Create( Request $request, $device )
    {
        $user_id = JwtController::getSub( $request );

        $selectedDevice = Device::firstOrNew([
            'user_id' => $user_id,
            'name'    => $device
        ]);

        if ( $selectedDevice->exists === true )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: device already exists'
            ], 409 )->send();

        if ( $selectedDevice->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: malformed field'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



    /* *
     *
     *  Deletes a device from the given user
     *
     * */
    public function Remove( Request $request, $device )
    {
        $user_id = JwtController::getSub( $request );

        $selectedDevice = Device::where('user_id', $user_id)->where('name', $device);

        if ( $selectedDevice->delete() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: malformed field'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



    /* *
     *
     * Retrieves N messages for a given user-device pair
     *
     * */
    public function GetMessages ( Request $request, $device, $number = 1 )
    {

        $user_id = JwtController::getSub( $request );

        return Message::select('messages.message', 'messages.created_at')

            ->join('devices', 'devices.id', '=', 'messages.device_id')
                ->where('devices.user_id', $user_id)
                ->where('devices.name', $device)

            ->where('messages.user_id', $user_id)
            ->orderBy('messages.id', 'desc')
            ->limit($number, 10)
            ->get();

    }



    /* *
     *
     *  Creates a new message for the given user's device
     *
     * */
    public function PostMessage ( Request $request, $device )
    {
        $user_id = JwtController::getSub( $request );

        # Check if there are a good message in the input
        $validator = Validator::make($request->all(), [
            'message' => [
                'required',
                'regex:/^(for|from){1}[\|]{1}[a-z0-9]{12}[\|]{1}[a-z]+([\|]{1}[a-z]+[\#]{1}[a-z0-9]+)*$/'    
            ]
        ]);

        if ($validator->fails()) 
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: malformed field'
            ], 400 )->send();

        # Get the id of a device and check
        $selectedDevice = Device::where('name', $device)
            ->where('user_id', $user_id)
            ->first();

        if ( is_null( $selectedDevice ) )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: device not found'
            ], 400 )->send();

        # Create a new message
        $newMessage = new Message;

        $newMessage->user_id   = $user_id;
        $newMessage->device_id = $selectedDevice->id;
        $newMessage->message   = $request->input('message');

        if ( $newMessage->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: message could not be stored'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



    /* *
     *
     *  Set new value for device
     *
     * */
    public function ChangeProfile( Request $request, $device ) 
    {
        $user_id = JwtController::getSub( $request );

        # Check input fields
        $validator = Validator::make($request->all(), [
            'type'        => 'string|nullable|max:50',
            'description' => 'string|nullable|max:50',
        ]);

        if ( $validator->fails() ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: malformed field'
            ], 400 )->send();
        }

        # Save only present fields
        $newValues = [];
        if ( $request->has('type') )
            $newValues['type'] = $request->input('type');

        if ( $request->has('description') )
            $newValues['description'] = $request->input('description');

        # Try to save coordinates and check errors
        $updatedDevice = Device::where('user_id', $user_id)
            ->where('name', $device)
            ->update($newValues);

        if ( $updatedDevice == false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: unable to save some field'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



    /* *
     *
     *  Retrieve the profile of a device
     *
     * */
    public function GetProfile( Request $request, $device )
    {
        $user_id = JwtController::getSub( $request );

        # Get the id of a device and check
        $profile = Device::select('name', 'type', 'description')
            ->where('name', $device)
            ->where('user_id', $user_id)
            ->first();

        if ( is_null( $profile ) )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: device not found'
            ], 400 )->send();
        
        return $profile;

    }

}
