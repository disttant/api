<?php

namespace App\Http\Controllers\V1;

use App\Device;
use App\Group;
use App\Relation;
use App\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\JwtController as JwtController;



class GroupController extends Controller
{



    /* *
     *
     *  List all available groups in the system
     *
     * */
    public static function List( Request $request )
    {

        $user_id = JwtController::getSub( $request );

        $groups =  Group::select('group')
            ->where('user_id', $user_id)
            ->get();

        if( count($groups) === 0 ){
            return [];
        }

        # Process the request a bit
        $result = [];
        foreach ($groups as $item => $data){
            $result['groups'][] = $data->group;
        }

        return $result;

    }



    /* *
     *
     *  List groups with related devices inside
     *
     * */
    public static function RelatedList( Request $request )
    {
        $user_id = JwtController::getSub( $request );

        # Get all related device with the group
        $relation = Relation::select('groups.group', 'devices.name', 'devices.type', 'devices.description')
            ->join('groups', 'relations.group_id', '=', 'groups.id')
                ->where('groups.user_id', $user_id)
            ->join('devices', 'relations.device_id', '=', 'devices.id')
                ->where('devices.user_id', $user_id)
            ->where('relations.user_id', $user_id)
            ->get();

        if( count($relation) === 0 ){
            return [ 'groups' => [] ];
        }

        # Process the request a bit
        $preResult = [];

        ## Step 1: Re-group devices into right groups
        foreach ($relation as $item => $data){
            $preResult[$data->group][] = [ 
                'name' => $data->name,
                'type' => $data->type,
                'description' => $data->description
            ];
        }

        ## Step 2: Change the structure a bit
        $index = 0;
        foreach ($preResult as $group => $devices){
            $result['groups'][$index]['name'] = $group;
            $result['groups'][$index]['devices'] = $devices;

            $index++;
        }

        return $result;
    }



    /* *
     *
     *  List groups with/without related devices inside
     *
     * */
    public static function FullList( Request $request )
    {
        $user_id = JwtController::getSub( $request );

        # Get needed information for the request
        $allGroups      = self::List( $request );
        $relatedGroups  = self::RelatedList( $request );

        if( !array_key_exists('groups', $allGroups) || ( count($allGroups['groups']) === 0 ) ){
            return [ 'groups' => [] ];
        }

        # Process the request a bit
        ## Step 1: Deleting the related groups of entire list
        foreach( $relatedGroups['groups'] as $item => $values )
        {
            $key = array_search($values['name'], $allGroups['groups']);
            unset( $allGroups['groups'][$key] );
        }

        ## Step 2: Adding empty groups to the list
        $index = count($relatedGroups['groups']);
        foreach( $allGroups['groups'] as $item )
        {
            $relatedGroups['groups'][$index]['name'] = $item;
            $relatedGroups['groups'][$index]['devices'] = [];
            $index++;
        }

        return $relatedGroups;
    }



    /* *
     *
     *  List selected group with related devices inside
     *
     * */
    public static function RelatedTo( Request $request, $group )
    {
        $user_id = JwtController::getSub( $request );

        # Get all group-related devices
        $relation = Relation::select('groups.group', 'devices.name', 'devices.type', 'devices.description')
            ->join('groups', 'relations.group_id', '=', 'groups.id')
                ->where('groups.user_id', $user_id)
                ->where('groups.group', $group)
            ->join('devices', 'relations.device_id', '=', 'devices.id')
                ->where('devices.user_id', $user_id)
            ->where('relations.user_id', $user_id)
            ->get();

        if( $relation->count() === 0 ){
            return [ 'group' => [] ];
        }

        # Process the request a bit
        $preResult = [];

        ## Step 1: Re-group devices into right groups
        foreach ($relation as $item => $data){
            $preResult[$data->group][] = [ 
                'name' => $data->name,
                'type' => $data->type,
                'description' => $data->description,
            ];
        }

        ## Step 2: Change the structure a bit
        $index = 0;
        foreach ($preResult as $group_name => $devices){
            $result['group'][$index]['name']    = $group_name;
            $result['group'][$index]['devices'] = $devices;

            $index++;
        }

        return $result;
    }



    /* *
     *
     * Retrieves N messages for a given user-group pair
     *
     * */
    public static function GetMessages( Request $request, $group, $number = 1 )
    {
        $user_id = JwtController::getSub( $request );

        return Message::select('messages.message', 'messages.created_at')
            ->joinWhere('groups', 'groups.group', '=', $group )
            ->join('relations', 'relations.group_id', '=', 'groups.id')
            ->whereColumn('messages.device_id', 'relations.device_id')

            ->where('relations.user_id', $user_id)
            ->where('groups.user_id', $user_id)
            ->where('messages.user_id', $user_id)

            ->orderBy('messages.id', 'desc')
            ->limit($number, 10)
            ->get();
    }



    /* *
     *
     *  Creates a new group for the given user
     *
     * */
    public static function Create( Request $request, $group )
    {
        $user_id = JwtController::getSub( $request );

        $selectedGroup = Group::firstOrNew([
            'user_id' => $user_id,
            'group'   => $group
        ]);

        if ( $selectedGroup->exists === true )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: resource already exists'
            ], 409 )->send();

        if ( $selectedGroup->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: malformed field'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



    /* *
     *
     *  Deletes a group from the given user
     *
     * */
    public static function Remove( Request $request, $group )
    {
        $user_id = JwtController::getSub( $request );

        $deletedRows = Group::where('user_id', $user_id)
            ->where('group', $group);

        if ( $deletedRows->delete() == false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: malformed field'
            ], 400 )->send();

        return response( '', 204 )->send();

    }



}
