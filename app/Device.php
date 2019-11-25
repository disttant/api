<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{



    public    $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'description'
    ];



    /* *
     *
     *  List all available devices of the given user
     *
     * */
    public static function List(string $user_id = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        return Device::select('name', 'type', 'description')
            ->where('user_id', $user_id)
            ->get();
    }



    /* *
     *
     *  List all not related devices of the given user
     *
     * */
    public static function Free(string $user_id = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

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
    public static function Create(string $user_id = null, string $name = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($name) || empty($name) )
            return false;

        $device = Device::firstOrNew([
            'user_id' => $user_id,
            'name' => $name
        ]);

        if ( $device->exists === true )
            return null;

        if ( $device->save() === false )
            return false;

        return true;

    }



    /* *
     *
     *  Deletes a device from the given user
     *
     * */
    public static function Remove(string $user_id = null, string $name = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($name) || empty($name) )
            return false;

        $deletedRows = Device::where('user_id', $user_id)->where('name', $name);
        $deletedRows->delete();

        return true;

    }



    /* *
     *
     *  Set new value for device
     *
     * */
    public static function Change( string $user_id = null, string $device = null, array $changes = [])
    {

        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($device) || empty($device) )
            return false;

        if ( is_null($changes) || empty($changes) )
            return false;

        # Try to update that device
        $updateDevice = Device::where('user_id', $user_id)
            ->where('name', $device)
            ->update($changes);

        if ( $updateDevice == false )
            return null;

        return true;
    }



    /* *
     *
     * Retrieves N messages for a given user-device pair
     *
     * */
    public static function GetMessages(string $user_id = null, string $device = null, $limit = 1)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        if ( is_null($device) || empty($device) )
            return [];

        return Message::select('message', 'created_at')
            ->orderByDesc(
                Device::select('name')
                    ->whereColumn('device_id', 'devices.id')
                    ->where('name', $device)
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'desc')
                    ->limit(1)
            )
            ->where('user_id', $user_id)
            ->limit($limit, 10)
            ->get();
    }



    /* *
     *
     *  Creates a new message for the given user-device pair
     *
     * */
    public static function SetMessage(string $user_id = null, string $device = null, string $message = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($device) || empty($device) )
            return false;

        if ( is_null($message) || empty($message) )
            return false;

        # Get the device_id of a device name
        $device_id = Device::where('name', $device)
            ->where('user_id', $user_id)
            ->first();

        if ( is_null( $device_id ) )
            return false;
        
        $device_id = $device_id->id;
        
        # Create a new message
        $newMessage = new Message;

        $newMessage->user_id = $user_id;
        $newMessage->device_id = $device_id;
        $newMessage->message = $message;

        if ( $newMessage->save() === false )
            return false;

        return true;
    }



}
