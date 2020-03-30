<?php

namespace App\Http\Controllers;

use App\Node;
use App\Group;
use App\Device;
use App\Relation;
use App\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Http\Controllers\JwtController as JwtController;
use App\Http\Controllers\NodeController as NodeController;
use App\Http\Controllers\GroupController as GroupController;
use App\Http\Controllers\DeviceController as DeviceController;



class RelationController extends Controller
{



    /* *
     *
     *  Create a new relation between a device and a group
     *
     * */
    public static function CreateOne( Request $request )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # is it a master?
        if( ! NodeController::isMaster( $request ) ){

            # Action not allowed
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Not allowed key'
            ], 400 )->send();
        }

        # Check if the body is right
        $validator = Validator::make($request->all(), [
            'group' => [
                'required',
                'regex:/^[a-z0-9]{1,30}$/',
                Rule::exists('groups', 'name')->where(function ($query) use ($jwtKeyring) {
                    return $query->where('node_id', $jwtKeyring['node_id']);
                }),
                
            ],
            'device' => [
                'required',
                'regex:/^[a-z0-9]{1,30}$/',
                Rule::exists('devices', 'name')->where(function ($query) use ($jwtKeyring) {
                    return $query->where('node_id', $jwtKeyring['node_id']);
                }),
            ]
        ]);

        # Check for errors on input data
        if ($validator->fails())
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Input field malformed or related resource does not exist'
            ], 400 )->send();

        # Look for the group and the device
        $selectDevice = DeviceController::GetOne( $request, $request->input('device'), true );
        $selectGroup  = GroupController::GetOne( $request, $request->input('group'), true );

        # Check for empty array: means not found
        if( (count($selectDevice['device']) === 0) || (count($selectGroup['group']) === 0) ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Related resource not found'
            ], 400 )->send();
        }

        # Check relation existance and save it
        $newRelation = Relation::firstOrNew([
            'device_id' => $selectDevice['device']['id'],
            'group_id'  => $selectGroup['group']['id'],
            'node_id'   => $jwtKeyring['node_id']
        ]);

        if ( $newRelation->exists() === true ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to save the resource. Resource exists'
            ], 400 )->send();
        }

        if ( $newRelation->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to save the resource'
            ], 400 )->send();

        return response([
            'relation' => [
                'device' => $request->input('device'),
                'group'  => $request->input('group')
            ]
        ], 200 )->send();
    }



    /* *
     *
     *  Remove a relation
     *
     * */
    public static function RemoveOne( Request $request, $device )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # is it a master or user?
        if( ! NodeController::isMaster( $request ) ){

            # Action not allowed
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Not allowed key'
            ], 400 )->send();
        }

        # Get the device info
        $selectDevice = DeviceController::GetOne( $request, $device, true );

        # Check for empty array: means not found
        if( count($selectDevice['device']) === 0 ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Related resource not found'
            ], 400 )->send();
        }

        # Delete the relation
        $deleteRelation = Relation::where('device_id', $selectDevice['device']['id'])
            ->where('node_id', $jwtKeyring['node_id'])
            ->delete();

        # Check for errors
        if ( $deleteRelation == false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to delete the resource'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



}
