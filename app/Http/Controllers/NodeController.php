<?php

namespace App\Http\Controllers;

use App\Node;
// use App\Group;
// use App\Device;
// use App\Relation;
// use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\JwtController as JwtController;

class NodeController extends Controller
{
    /* *
     *
     *  Return true if a master is identified for a node
     *
     * */
    public static function isMaster( Request $request )
    {
        $jwtCard = JwtController::getCard( $request );

        $master =  Node::select('id')
            ->where('id', $jwtCard['node_id'])
            ->where('key', $jwtCard['key'])
            ->limit(1)
            ->get();

        if( $master->isEmpty() ){
            return false;
        }

        return true;
    }



    /* *
     *
     *  Return true if the owner of a node is identified from the JWT
     *
     * */
    public static function isOwner( Request $request )
    {
        $jwtUserId  = JwtController::getSub( $request );
        $jwtCard = JwtController::getCard( $request );

        $owner =  Node::select('id')
            ->where('id', $jwtCard['node_id'])
            ->where('key', $jwtCard['key'])
            ->where('user_id', $jwtUserId)
            ->limit(1)
            ->get();

        if( $owner->isEmpty() ){
            return false;
        }

        return true;
    }



    /* *
     *
     *  Create new node
     *
     * */
    public static function CreateOne( Request $request )
    {
        # Check if the body is right
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'regex:/^[a-z0-9]{1,30}$/',
                'unique:nodes,name'
            ],
            'user_id' => [
                'required',
                'regex:/^[0-9]+$/',
            ],
            'key' => [
                'required',
                'regex:/^[a-z0-9]{64}$/',
                'unique:nodes,key',
                'unique:groups,key'
            ],
        ]);

        # Check for errors on input data
        if ($validator->fails()){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Input field malformed or exists'
            ], 400 )->send();
        }

        # Save into DB
        $newNode          = new Node;
        $newNode->name    = $request->input('name');
        $newNode->user_id = $request->input('user_id');
        $newNode->key     = $request->input('key');

        # Check for errors saving data
        if ( $newNode->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to save the resource'
            ], 400 )->send();

        # Success, answer with the new resource
        return response()->json( [
            'node' => [
                'id'       => $newNode->id,
                'name'     => $request->input('name'),
                'key'      => $request->input('key'),
            ]
        ], 200 )->send();
    }



    /* *
     *
     *  Set new value for a node
     *
     * */
    public static function ChangeOne( Request $request ) 
    {
        # Check if the body is right
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'regex:/^[0-9]+$/',
                'exists:nodes',
            ],
            'name' => [
                'regex:/^[a-z0-9]{1,30}$/',
            ],
            'user_id' => [
                'required',
                'regex:/^[0-9]+$/',
            ],
            'key' => [
                'regex:/^[a-z0-9]{64}$/',
                'unique:nodes,key',
                'unique:groups,key'
            ],
        ]);

        # Check for errors on input data
        if ($validator->fails()){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Input field malformed or exists'
            ], 400 )->send();
        }

        # Retrieve node from the db
        $updateNode = Node::where('id', $request->input('id'))
            ->where('user_id', $request->input('user_id'))
            ->first();

        # Request has null fields?
        if( $request->has('name') ){
            $updateNode->name = $request->input('name');
        }

        if( $request->has('key') ){
            $updateNode->key = $request->input('key');
        }

        # Save and check errors
        if ( $updateNode->save() == false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to update the resource'
            ], 400 )->send();

        return response()->json( [
            'node' => [
                'id'          => $request->input('id'),
                'name'        => $request->input('name'),
                'key'         => $request->input('key')
            ]
        ], 200 )->send();
    }



    /* *
     *
     *  Delete a node
     *
     * */
    public static function RemoveOne( int $nodeId, int $userId )
    {
        # Find and delete the resource
        $deleteNode = Node::where('id', $nodeId)
            ->where('user_id', $userId)
            ->delete();

        if ( $deleteNode == false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to delete the resource'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



    /* *
     *
     *  Get all nodes for a user_id (owner)
     *
     * */
    public static function GetAll( int $userId )
    {
        $nodes = Node::select('id', 'name', 'key')
                    ->where('user_id', $userId)
                    ->get();
        
        # Return empty structure
        if( $nodes->isEmpty() ){
            return [
                    'nodes' => []
            ];
        }

        # Process the request a bit
        $result = [];
        foreach ($nodes as $item => $data){
            $result['nodes'][] = [
                'id'       => $data->id,
                'name'     => $data->name,
                'key'      => $data->key,
            ];
        }

        # Return the results
        return $result;
    }



    /* *
     *
     *  Show all nodes. JSON response
     *
     * */
    public static function ShowAll( int $userId )
    {
        $data = self::GetAll( $userId );

        return response()->json( $data , 200 )->send();
    }

}