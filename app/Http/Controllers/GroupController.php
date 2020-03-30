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


class GroupController extends Controller
{



    /* *
     *
     *  Create a new group
     *
     * */
    public static function CreateOne( Request $request )
    {
        # is it a master or user?
        if( ! NodeController::isMaster( $request ) ){

            # Action not allowed
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Not allowed key'
            ], 400 )->send();

        }

        $jwtKeyring = JwtController::getKeyring( $request );

        # Check if the body is right
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'regex:/^[a-z0-9]{1,30}$/',
                Rule::unique('groups')->where(function ($query) use ($jwtKeyring) {
                    return $query->where('node_id', $jwtKeyring['node_id']);
                })
            ],
            'key' => [
                'regex:/^[a-z0-9]{64}$/',
                'unique:nodes,key',
                'unique:groups,key'
            ]
        ]);

        # Check for errors on input data
        if ($validator->fails())
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Input field malformed or exists'
            ], 400 )->send();

        # Request has a null key or filled?
        if( !$request->has('key') ){
            $lockKey = null;
        }else{
            $lockKey = $request->input('key');
        }

        # Save into DB
        $newGroup = new Group;
        $newGroup->name = $request->input('name');
        $newGroup->node_id = $jwtKeyring['node_id'];
        $newGroup->key = $lockKey;

        # Check for errors saving data
        if ( $newGroup->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to save the resource'
            ], 400 )->send();

        # Success, answer with the new resource
        return response()->json( [
            'group' => [
                'name'     => $request->input('name'),
                'key' => $lockKey
            ]
        ], 200 )->send();
    }



    /* *
     *
     *  Delete a group
     *
     * */
    public static function RemoveOne( Request $request, string $group )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # is it a master or user?
        if( ! NodeController::isMaster( $request ) ){

            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Not allowed key'
            ], 400 )->send();
        }

        $deleteGroup = Group::where('name', $group)
            ->where('node_id', $jwtKeyring['node_id'])
            ->delete();

        # Check deletion
        if ( $deleteGroup == false ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to delete the resource'
            ], 400 )->send();
        }

        # Group was deleted
        return response( '', 204 )->send();
    }



    /* *
     *
     *  Get all info for a defined group
     *
     * */
    public static function GetOne ( Request $request, string $group, bool $showId = false)
    {
        # Get groups allowed by your key
        $allGroups = self::GetAll( $request, $showId );

        # Look for the group you need
        $key = array_search( $group, array_column($allGroups['groups'], 'name') );

        # Group not found
        if( $key === false ){
            return [
                'group' => []
            ];
        }

        # Return the results
        return [
            'group' => $allGroups['groups'][$key]
        ];
    }



    /* *
     *
     *  Show all info for a defined group
     *
     * */
    public static function ShowOne ( Request $request, string $group )
    {
        # Get groups allowed by your key
        $data = self::GetOne( $request, $group );

        # Check for empty array: means not found
        if( count($data['group']) === 0 ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Resource not found'
            ], 400 )->send();
        }

        # Return the result
        return response()->json($data, 200 )->send();
    }



    /* *
     *
     *  Get all group names
     *
     *
     * */
    public static function GetNames( Request $request, bool $showId = false)
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # Prepare all the group names
        $groups = Group::select('id', 'name')
                      ->where('node_id', $jwtKeyring['node_id']);

        # Not the master? ask for key too
        if( !NodeController::isMaster( $request ) ){
            $groups =  $groups->where('key', $jwtKeyring['key']);
        }

        # Get the results
        $groups = $groups->get();

        # No results: return empty structure
        if( $groups->isEmpty() ){

            return [
                'groups' => []
            ];
        }

        # Process the request a bit
        if($showId){
            foreach ($groups as $item => $data){
                $results[] = [
                    'id'   => $data->id,
                    'name' => $data->name
                ];
            }
        }else{
            foreach ($groups as $item => $data){
                $results[] = [
                    'name' => $data->name
                ];
            }
        }

        # Return the results
        return [
            'groups' => $results
        ];
    }



    /* *
     *
     *  Show all group names. JSON response
     *
     * */
    public static function ShowNames( Request $request )
    {
        $data = self::GetNames( $request );

        return response()->json( $data , 200 )->send();
    }



    /* *
     *
     *  Get groups with related devices only
     *
     * */
    public static function GetRelated( Request $request, bool $showId = false )
    {
        $keyring = JwtController::getKeyring( $request );

        # Get all groups with relations
        $groups = Relation::select(
                        'groups.id as groupId',
                        'groups.name as groupName', 
                        'devices.name', 
                        'devices.type', 
                        'devices.description'
                    )
                    ->join('groups', 'relations.group_id', '=', 'groups.id')
                    ->where('groups.node_id', $keyring['node_id'])
                    ->join('devices', 'relations.device_id', '=', 'devices.id')
                    ->where('devices.node_id', $keyring['node_id'])
                    ->where('relations.node_id', $keyring['node_id']);

        # Not the master? ask for key
        if( ! NodeController::isMaster( $request ) ){
            $groups = $groups->where('groups.key', $keyring['key']);
        }

        # Retrieve the results
        $groups = $groups->get();

        # If not results return empty structure
        if( $groups->isEmpty() ){

            return [
                'groups' => []
            ];
        }

        # Give structure to data
        ## Step 1: Re-group devices by group name
        foreach ($groups as $item => $data){
            $preResult[$data->groupName]['id'] = $data->groupId;
            $preResult[$data->groupName]['devices'][] = [
                'name' => $data->name,
                'type' => $data->type,
                'description' => $data->description
            ];
        }

        ## Step 2: Change the structure a bit
        if( $showId ){
            foreach ($preResult as $group => $item){
                $results[] = [
                    'id'      => $item['id'],
                    'name'    => $group,
                    'devices' => $item['devices']
                ];
            }
        }else{
            foreach ($preResult as $group => $item){
                $results[] = [
                    'name'    => $group,
                    'devices' => $item['devices']
                ];
            }
        }

        # Return the results
        return [
            'groups' => $results
        ];
    }



    /* *
     *
     *  Show groups with related devices only. JSON response
     *
     * */
    public static function ShowRelated( Request $request )
    {
        $data = self::GetRelated( $request );

        return response()->json( $data , 200 )->send();
    }



    /* *
     *
     *  List all groups with/without devices inside
     *
     * */
    public static function GetAll( Request $request, bool $showId = false)
    {
        # Get needed information for the request
        $listNames    = self::GetNames( $request, $showId);
        $listRelated  = self::GetRelated( $request, $showId);

        # If no groups found, return empty structure
        if( count($listNames['groups']) === 0 ){
            return [
                'groups' => []
            ];
        }

        # Give structure to data
        ## Step 1: Deleting the related groups of entire list
        foreach( $listRelated['groups'] as $item => $values ){
            $key = array_search($values['name'], array_column($listNames['groups'], 'name') );
            unset( $listNames['groups'][$key] );
        }

        ## Step 2: Adding empty groups to the list
        $index = count($listRelated['groups']);
        foreach( $listNames['groups'] as $item ){
            $listRelated['groups'][$index] = $item;
            $listRelated['groups'][$index]['devices'] = [];
            $index++;
        }

        # Return the results
        return $listRelated;
    }



    /* *
     *
     *  Show all groups with/without devices inside. JSON response
     *
     * */
    public static function ShowAll( Request $request )
    {
        $data = self::GetAll( $request);

        return response()->json( $data , 200 )->send();
    }



    /* *
     *
     * Get {number} messages of a group
     *
     * */
    public static function GetMessages( Request $request, string $group, int $number = 1 )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # Set a limit in messages number
        $limit = 10;

        if ( $number > $limit ){ $number = $limit; }

        # Prepare all messages for that group
        $messages =  Message::select(
                            'messages.message', 
                            'messages.created_at'
                        )
                        ->joinWhere('groups', 'groups.name', '=', $group )
                        ->join('relations', 'relations.group_id', '=', 'groups.id')
                        ->whereColumn('messages.device_id', 'relations.device_id')

                        ->where('relations.node_id', $jwtKeyring['node_id'])
                        ->where('groups.node_id', $jwtKeyring['node_id'])
                        ->where('messages.node_id', $jwtKeyring['node_id'])

                        ->orderBy('messages.id', 'desc')
                        ->limit($number, 10);

        # Not the master? ask for key
        if( ! NodeController::isMaster( $request ) ){
            $messages = $messages->where('groups.key', $keyring['key']);
        }

        # Get the messages
        $messages = $messages->get();

        # Return the results
        return [
            'group' => [
                'name' => $group,
                'messages' => $messages
            ]
        ];
    }



    /* *
     *
     *  Show {number} messages of a group. JSON response
     *
     * */
    public static function ShowMessages( Request $request, string $group, int $number )
    {
        $data = self::GetMessages( $request, $group, $number );

        return response()->json( $data , 200 )->send();
    }


}