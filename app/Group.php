<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    
    public    $timestamps = false;


    protected $fillable = [
        'user_id',
        'group'
    ];


    /* *
     *
     *  List all available groups in the system
     *
     * */
    public static function List(string $user_id = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        return Group::select('group')
            ->where('user_id', $user_id)
            ->get();
    }



    /* *
     *
     *  List groups with related channels inside
     *
     * */
    public static function Related( string $user_id = null )
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        # Get all related channel with the group
        $relation = Relation::select('groups.group', 'channels.channel')
            ->join('groups', 'relations.group_id', '=', 'groups.id')
                ->where('groups.user_id', $user_id)
            ->join('channels', 'relations.channel_id', '=', 'channels.id')
                ->where('channels.user_id', $user_id)
            ->where('relations.user_id', $user_id)
            ->get();

        if( count($relation) === 0 ){
            return [];
        }

        # Process the request a bit
        $result = [];
        foreach ($relation as $item => $data){
            $result[$data->group][] = $data->channel;
        }

        return $result;
    }



    /* *
     *
     *  Creates a new group for the given user
     *
     * */
    public static function Create(string $user_id = null, string $name = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($name) || empty($name) )
            return false;

        $group = Group::firstOrNew([
            'user_id' => $user_id,
            'group' => $name
        ]);

        if ( $group->exists === true )
            return null;

        if ( $group->save() === false )
            return false;

        return true;

    }



    /* *
     *
     *  Deletes a group from the given user
     *
     * */
    public static function Remove(string $user_id = null, string $name = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($name) || empty($name) )
            return false;

        $deletedRows = Group::where('user_id', $user_id)->where('group', $name);
        $deletedRows->delete();

        return true;

    }



    


    /* *
     *
     * Retrieves N messages for a given user-group pair
     *
     * */
    public static function GetMessages(string $user_id = null, string $group = null, $limit = 1)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        if ( is_null($group) || empty($group) )
            return [];


        return Message::select('messages.message', 'messages.created_at')
            ->joinWhere('groups', 'groups.group', '=', $group )
            ->join('relations', 'relations.group_id', '=', 'groups.id')
            ->whereColumn('messages.channel_id', 'relations.channel_id')

            ->where('relations.user_id', $user_id)
            ->where('groups.user_id', $user_id)
            ->where('messages.user_id', $user_id)

            ->orderBy('messages.id', 'desc')
            ->limit($limit, 10)
            ->get();

    }


}



