<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    //
    protected $primaryKey = 'user_id';
    
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'device_id',
        'group_id',
        'map_x',
        'map_y'
    ];



    /* *
     *
     *  Creates a new device relation into the given group
     *
     * */
    public static function Create(string $user_id = null, string $device_name = null, string $group_name = null)
    {

        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($device_name) || empty($device_name) )
            return false;

        if ( is_null($group_name) || empty($group_name) )
            return false;

        # Check if group and device exists
        $group = Group::where('user_id', $user_id)
            ->where('group', $group_name)
            ->first();

        $device = Device::where('user_id', $user_id)
            ->where('name', $device_name)
            ->first();
        
        if ( is_null( $group ) || is_null( $device ) )
            return false;

        $group_id = $group->id;
        $device_id = $device->id;

        # Create a new relation model
        $relation = Relation::firstOrNew([
            'user_id'    => $user_id,
            'device_id' => $device_id,
            'group_id'   => $group_id
        ]);

        if ( $relation->exists === true )
            return null;

        if ( $relation->save() === false )
            return false;

        return true;
    }



    /* *
     *
     *  Removes a device relation
     *
     * */
    public static function Remove( string $user_id = null, string $device_name = null )
    {

        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($device_name) || empty($device_name) )
            return false;

        # Check if device exists
        $device = Device::where('user_id', $user_id)
            ->where('name', $device_name)
            ->first();
        
        if ( is_null( $device ) )
            return false;

        $device_id = $device->id;

        # Try to remove any relation for that device
        $deleteRelation = Relation::where('user_id', $user_id)
            ->where('device_id', $device_id)
            ->delete();

        if ( $deleteRelation == false )
            return null;

        return true;
    }



    /* *
     *
     *  Set new value for device relation
     *
     * */
    public static function Change( string $user_id = null, string $device_name = null, array $changes = [])
    {

        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($device_name) || empty($device_name) )
            return false;

        if ( is_null($changes) || empty($changes) )
            return false;

        # Check if device exists
        $device = Device::where('user_id', $user_id)
            ->where('name', $device_name)
            ->first();
        
        if ( is_null( $device ) )
            return false;

        $device_id = $device->id;

        # Try to update any relation for that device
        $updateRelation = Relation::where('user_id', $user_id)
            ->where('device_id', $device_id)
            ->update($changes);

        if ( $updateRelation == false )
            return null;

        return true;
    }
    
}
