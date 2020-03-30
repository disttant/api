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



class DeviceController extends Controller
{



    /* *
     *
     *  Create new device
     *
     * */
    public static function CreateOne( Request $request )
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

        # Check if the body is right
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'regex:/^[a-z0-9]{1,30}$/',
                Rule::unique('devices')->where(function ($query) use ($jwtKeyring) {
                    return $query->where('node_id', $jwtKeyring['node_id']);
                })
            ],
            'type' => [
                'regex:/^[a-z0-9-_]{1,50}$/',
            ],
            'description' => [
                'regex:/^[a-zA-Z0-9-_\s]{1,50}$/',
            ]
        ]);

        # Check for errors on input data
        if ($validator->fails())
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Input field malformed or exists'
            ], 400 )->send();

        # Generate a new resource
        $newDevice = new Device;
        $newDevice->name = $request->input('name');
        $newDevice->node_id = $jwtKeyring['node_id'];

        # Request has optional fields?
        if( $request->has('type') ){
            $newDevice->type = $request->input('type');
        }

        if( $request->has('description') ){
            $newDevice->description = $request->input('description');
        }

        # Check for errors saving data
        if ( $newDevice->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to save the resource'
            ], 400 )->send();

        # Success, answer with the new resource
        return response()->json( [
            'device' => [
                'name'        => $request->input('name'),
                'type'        => $type,
                'description' => $description
            ]
        ], 200 )->send();
    }



    /* *
     *
     *  Set new value for a device
     *
     * */
    public static function ChangeOne( Request $request ) 
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
            'name' => [
                'required',
                'regex:/^[a-z0-9]{1,30}$/',
                Rule::exists('devices')->where(function ($query) use ($jwtKeyring) {
                    return $query->where('node_id', $jwtKeyring['node_id']);
                })
            ],
            'type' => [
                'regex:/^[a-z0-9-_]{1,50}$/',
            ],
            'description' => [
                'regex:/^[a-zA-Z0-9-_\s]{1,50}$/',
            ]
        ]);

        # Check for errors on input data
        if ($validator->fails())
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Input field malformed or exists'
            ], 400 )->send();

        # Retrieve resource from the database
        $updateDevice = Device::where('name', $request->input('name'))
                            ->where('node_id', $jwtKeyring['node_id'])
                            ->get();

        # Request has null fields?
        if( $request->has('type') ){
            $updateDevice->type = $request->input('type');
        }

        if( $request->has('description') ){
            $updateDevice->description = $request->input('description');
        }

        # Try to save and check for errors
        if ( $updateDevice->save() === false ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to update the resource'
            ], 400 )->send();
        }

        return response()->json( [
            'device' => [
                'name'        => $request->input('name'),
                'type'        => $type,
                'description' => $description
            ]
        ], 200 )->send();
    }



    /* *
     *
     *  Delete a device
     *
     * */
    public static function RemoveOne( Request $request, string $device )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # is it a master or user?
        if( ! NodeController::isMaster( $request ) ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Not allowed key'
            ], 400 )->send();
        }

        $deleteDevice = Device::where('name', $device)
            ->where('node_id', $jwtKeyring['node_id'])
            ->delete();

        if ( $deleteDevice == false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to delete the resource'
            ], 400 )->send();

        return response( '', 204 )->send();
    }



    /* *
     *
     *  Get data of a device
     *
     * */
    public static function GetOne( Request $request, string $device, bool $showId = false )
    {
        # Get needed information for the request
        $allDevices = self::GetAll( $request, $showId );

        # Look for the device
        $key = array_search($device, array_column($allDevices['devices'], 'name') );
        
        if ( $key === false ){
            return [
                'device' => []
            ];
        }

        return [
            'device' => $allDevices['devices'][$key]
        ];
    }



    /* *
     *
     *  Show data of a device. JSON response
     *
     * */
    public static function ShowOne( Request $request, string $device )
    {
        # Get groups allowed by your key
        $data = self::GetOne( $request, $device );

        # Check for empty array: means not found
        if( count($data['device']) === 0 ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Resource not found'
            ], 400 )->send();
        }

        # Return the result
        return response()->json( $data , 200 )->send();
    }



    /* *
     *
     *  Get all devices
     *
     * */
    public static function GetAll( Request $request, bool $showId = false )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # is it a master or user?
        if( NodeController::isMaster( $request ) ){

            $devices =  Device::select('id', 'name', 'type', 'description')
                ->where('node_id', $jwtKeyring['node_id'])
                ->get();

        }else{

            $devices =  Device::select('devices.id', 'devices.name', 'devices.type', 'devices.description')
                ->where('devices.node_id', $jwtKeyring['node_id'])

                ->joinWhere('groups', 'groups.key', '=', $jwtKeyring['key'] )
                ->where('groups.node_id', $jwtKeyring['node_id'])

                ->join('relations', 'relations.group_id', '=', 'groups.id')
                ->whereColumn('devices.id', 'relations.device_id')
                ->where('relations.node_id', $jwtKeyring['node_id'])

                ->get();
        }
        
        # Return empty structure
        if( $devices->isEmpty() ){
            return [
                    'devices' => []
            ];
        }

        # Process the request a bit
        $result = [];
        if( $showId === true){
            foreach ($devices as $item => $data){
                $result['devices'][] = [
                    'id'          => $data->id,
                    'name'        => $data->name,
                    'type'        => $data->type,
                    'description' => $data->description
                ];
            }
        }else{
            foreach ($devices as $item => $data){
                $result['devices'][] = [
                    'name'        => $data->name,
                    'type'        => $data->type,
                    'description' => $data->description
                ];
            }
        }

        # Return the results
        return $result;
    }



    /* *
     *
     *  Show all devices. JSON response
     *
     * */
    public static function ShowAll( Request $request )
    {
        $data = self::GetAll( $request );

        return response()->json( $data , 200 )->send();
    }



    /* *
     *
     *  Get all not related devices of the given user
     *
     * */
    public static function GetFree( Request $request )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # is it a master or user?
        if( ! NodeController::isMaster( $request ) ){
            return [
                'devices' => []
            ];
        }

        $devices = Device::select('name', 'type', 'description')
                    ->where('node_id', $jwtKeyring['node_id'])
                    ->whereNotIn('id', 
                        Relation::select('device_id')
                            ->whereColumn('device_id', 'devices.id')
                            ->where('node_id', $jwtKeyring['node_id'])
                    )
                    ->get();

        # Return empty structure
        if( $devices->isEmpty() ){
            return [
                'devices' => []
            ];
        }

        # Process the request a bit
        $result = [];
        foreach ($devices as $item => $data){
            $result['devices'][] = [
                'name'        => $data->name,
                'type'        => $data->type,
                'description' => $data->description
            ];
        }

        # Return the results
        return $result;
    }



    /* *
     *
     *  Show all not related devices of the given user. JSON response
     *
     * */
    public static function ShowFree( Request $request )
    {
        $data = self::GetFree( $request );

        return response()->json( $data , 200 )->send();
    }



    /* *
     *
     *  Create a new message in a device's conversation
     *
     * */
    public static function CreateMessage ( Request $request )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # Check if the body is right
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'regex:/^[a-z0-9]{1,30}$/',
            ],
            'message' => [
                'required',
                'regex:/^(for|from){1}[\|]{1}[a-z0-9]{12}[\|]{1}[a-z]+([\|]{1}[a-z]+[\#]{1}[a-z0-9]+)*$/',
            ]
        ]);
        
        # Check for errors on input data
        if ($validator->fails()){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Input field is malformed'
            ], 400 )->send();
        }

        # Take the info of the device
        $device = self::GetOne( $request, $request->input('name'), true );

        # Create a new message
        $newMessage = new Message;
        $newMessage->device_id = $device['device']['id'];
        $newMessage->node_id   = $jwtKeyring['node_id'];
        $newMessage->message   = $request->input('message');

        # Check for errors
        if ( $newMessage->save() === false )
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bad request: Unable to save the resource'
            ], 400 )->send();

        # Return the results
        return response([
            'device' => [
                'name'    => $device['device']['name'],
                'message' => $request->input('message')
            ]
        ], 200 )->send();
    }


    
    /* *
     *
     * Get N messages from a device
     *
     * */
    public static function GetMessages ( Request $request, string $device, int $number = 1 )
    {
        $jwtKeyring = JwtController::getKeyring( $request );

        # Set a limit in messages number
        $limit = 10;

        if ( $number > $limit ){ $number = $limit; }

        # Take the info of the device
        $selectDevice = self::GetOne( $request, $device, true );

        # Get the messages from DB
        $messages = Message::select('messages.message', 'messages.created_at')
            ->where('messages.node_id', $jwtKeyring['node_id'])
            ->where('messages.device_id', $selectDevice['device']['id'])
            ->orderBy('messages.id', 'desc')
            ->limit($number)
            ->get();

        # Return the result
        return [
            'device' => [
                'name' => $selectDevice['device']['name'],
                'messages' => $messages
            ]
        ];
    }



    /* *
     *
     *  Show {number} messages of a device. JSON response
     *
     * */
    public static function ShowMessages( Request $request, string $device, int $number )
    {
        $data = self::GetMessages( $request, $device, $number );

        return response()->json( $data , 200 )->send();
    }



    



    



    

}
