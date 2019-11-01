<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{



    public    $timestamps = false;

    protected $fillable = [
        'user_id',
        'channel'
    ];



    /* *
     *
     *  List all available channels of the given user
     *
     * */
    public static function List(string $user_id = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        return Channel::select('channel')
            ->where('user_id', $user_id)
            ->get();
    }



    /* *
     *
     *  List all not related channels of the given user
     *
     * */
    public static function Free(string $user_id = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        return Channel::select('channel')
            ->where('user_id', $user_id)
            ->whereNotIn('id', 
                Relation::select('channel_id')
                    ->whereColumn('channel_id', 'channels.id')
                    ->where('user_id', $user_id)
            )
            ->get();
    }



    /* *
     *
     *  Creates a new channel for the given user
     *
     * */
    public static function Create(string $user_id = null, string $name = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($name) || empty($name) )
            return false;

        $channel = Channel::firstOrNew([
            'user_id' => $user_id,
            'channel' => $name
        ]);

        if ( $channel->exists === true )
            return null;

        if ( $channel->save() === false )
            return false;

        return true;

    }



    /* *
     *
     *  Deletes a channel from the given user
     *
     * */
    public static function Remove(string $user_id = null, string $name = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($name) || empty($name) )
            return false;

        $deletedRows = Channel::where('user_id', $user_id)->where('channel', $name);
        $deletedRows->delete();

        return true;

    }



    /* *
     *
     * Retrieves N messages for a given user-channel pair
     *
     * */
    public static function GetMessages(string $user_id = null, string $channel = null, $limit = 1)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        if ( is_null($channel) || empty($channel) )
            return [];

        return Message::
              select('message', 'created_at')
            ->orderByDesc(
                Channel::select('channel')
                    ->whereColumn('channel_id', 'channels.id')
                    ->where('channel', $channel)
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
     *  Creates a new message for the given user-channel pair
     *
     * */
    public static function SetMessage(string $user_id = null, string $channel = null, string $message = null)
    {
        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($channel) || empty($channel) )
            return false;

        if ( is_null($message) || empty($message) )
            return false;

        # Get the channel_id of a channel name
        $channel_id = Channel::where('channel', $channel)
            ->where('user_id', $user_id)
            ->first();

        if ( is_null( $channel_id ) )
            return false;
        
        $channel_id = $channel_id->id;
        
        # Create a new message
        $newMessage = new Message;

        $newMessage->user_id = $user_id;
        $newMessage->channel_id = $channel_id;
        $newMessage->message = $message;

        if ( $newMessage->save() === false )
            return false;

        return true;
    }



    /* *
     *
     *  Creates a new channel relation into the given group
     *
     * */
    public static function SetLink(string $user_id = null, string $channel_name = null, string $group_name = null)
    {

        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($channel_name) || empty($channel_name) )
            return false;

        if ( is_null($group_name) || empty($group_name) )
            return false;

        # Check if group and channel exists
        $group = Group::where('user_id', $user_id)
            ->where('group', $group_name)
            ->first();

        $channel = Channel::where('user_id', $user_id)
            ->where('channel', $channel_name)
            ->first();
        
        if ( is_null( $group ) || is_null( $channel ) )
            return false;

        $group_id = $group->id;
        $channel_id = $channel->id;

        # Create a new relation model
        $relation = Relation::firstOrNew([
            'user_id'    => $user_id,
            'channel_id' => $channel_id,
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
     *  Removes a channel relation
     *
     * */
    public static function RemoveLink( string $user_id = null, string $channel_name = null )
    {

        if ( is_null($user_id) || empty($user_id) )
            return false;

        if ( is_null($channel_name) || empty($channel_name) )
            return false;

        # Check if channel exists
        $channel = Channel::where('user_id', $user_id)
            ->where('channel', $channel_name)
            ->first();
        
        if ( is_null( $channel ) )
            return false;

        $channel_id = $channel->id;

        # Try to remove any relation for that channel
        $deleteRelation = Relation::where('user_id', $user_id)
            ->where('channel_id', $channel_id)
            ->delete();

        if ( $deleteRelation == false )
            return null;

        return true;
    }



}
