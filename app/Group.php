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
    public static function RelatedList( string $user_id = null )
    {
        if ( is_null($user_id) || empty($user_id) )
            return [ 'groups' => [] ];

        # Get all related device with the group
        $relation = Relation::select('groups.group', 'devices.name', 'devices.type', 'devices.description', 'relations.map_x', 'relations.map_y')
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
                'description' => $data->description,
                'map' => [$data->map_x, $data->map_y]
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
    public static function FullList( string $user_id = null )
    {
        if ( is_null($user_id) || empty($user_id) )
            return [ 'groups' => [] ];

        # Get needed information for the request
        $allGroups      = self::List($user_id);
        $relatedGroups  = self::RelatedList($user_id);

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
            ->whereColumn('messages.device_id', 'relations.device_id')

            ->where('relations.user_id', $user_id)
            ->where('groups.user_id', $user_id)
            ->where('messages.user_id', $user_id)

            ->orderBy('messages.id', 'desc')
            ->limit($limit, 10)
            ->get();

    }


}



