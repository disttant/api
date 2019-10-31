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
     *  Creates a new channel into the given home
     *
     * */
    public static function List(string $user_id = null, int $page = 1, int $perPage = 10)
    {
        if ( is_null($user_id) || empty($user_id) )
            return [];

        return Channel::select('channel')
            ->where('user_id', $user_id)
            ->forPage($page, $perPage)
            ->get();
    }



    /* *
     *  Creates a new channel into the given home
     *
     * */
    public static function Free(string $user_id = null, int $page = 1, int $perPage = 10)
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
            ->forPage($page, $perPage)
            ->get();
    }



    /* *
     *  Creates a new channel into the given home
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
     *  Deletes a channel from the given home
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
     * Retrieves N messages for a given home-channel pair
     *
     * */
    public static function GetMessages(string $user_id = null, string $channel = null, $limit = 10)
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
     *  Creates a new channel into the given home
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
            ->first()
            ->id;

        # Check if there is a channel id <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        
        # Create a new message
        $newMessage = new Message;

        $newMessage->user_id = $user_id;
        $newMessage->channel_id = $channel_id;
        $newMessage->message = $message;

        if ( $newMessage->save() === false )
            return false;

        return true;

    }



    
}
