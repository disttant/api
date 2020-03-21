<?php

namespace App\Http\Controllers\V1;

use App\Device;
use App\Group;
use App\Relation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\JwtController as JwtController;



class RelationController extends Controller
{



    /* *
     *
     *  Creates a new device relation into the given group
     *
     * */
    public static function Create( Request $request, $device, $group )
    {
        $user_id = JwtController::getSub( $request );

        # Check if group and device exists
        $selectedGroup = Group::where('user_id', $user_id)
            ->where('group', $group)
            ->first();

        $selectedDevice = Device::where('user_id', $user_id)
            ->where('name', $device)
            ->first();
        
        if ( is_null( $selectedGroup ) || is_null( $selectedDevice ) )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: device or group not found'
            ], 400 )->send();

        # Create a new relation model
        $newRelation = Relation::firstOrNew([
            'user_id'    => $user_id,
            'device_id'  => $selectedDevice->id,
            'group_id'   => $selectedGroup->id
        ]);

        if ( $newRelation->exists === true )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: relation already exists'
            ], 400 )->send();

        if ( $newRelation->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: malformed field or not found'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



    /* *
     *
     *  Removes a device relation
     *
     * */
    public static function Remove( Request $request, $device )
    {
        $user_id = JwtController::getSub( $request );

        # Check if device exists
        $selectedDevice = Device::where('user_id', $user_id)
            ->where('name', $device)
            ->first();
        
        if ( is_null( $device ) )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: malformed field or not found'
            ], 400 )->send();

        # Try to remove any relation for that device
        $deleteRelation = Relation::where('user_id', $user_id)
            ->where('device_id', $selectedDevice->id)
            ->delete();

        if ( $deleteRelation == false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: removal was not done'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



}
